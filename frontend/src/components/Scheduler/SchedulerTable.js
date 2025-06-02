import React, { useState, useEffect, useMemo } from 'react';
import {
    Box, Button, Typography, IconButton, Modal, Snackbar, Alert,
    Table, TableBody, TableCell, TableContainer, TableHead, TableRow,
    Select, MenuItem, InputLabel, FormControl, Tooltip, Checkbox, ListItemText
} from '@mui/material';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';
import { format, addDays, subDays, startOfWeek, isValid } from 'date-fns';
import ArrowForwardIcon from '@mui/icons-material/ArrowForward';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import { styled } from '@mui/system';
import { fetchShifts, generateSchedule, fetchQueues } from '../../api';

const shiftColors = ['#a5d6a7', '#81d4fa', '#ffcc80', '#f48fb1', '#ce93d8', '#90caf9', '#ffe082'];

const StyledTableCell = styled(TableCell)(({ isHighlighted }: any) => ({
    padding: '6px',
    textAlign: 'center',
    minWidth: '120px',
    border: '1px solid #ddd',
    backgroundColor: isHighlighted ? '#c5e1a5' : '#f1f1f1',
    verticalAlign: 'top',
}));

const StyledTableRow = styled(TableRow)({
    '&:nth-of-type(odd)': {
        backgroundColor: '#f9f9f9',
    },
});

const SchedulerTable = () => {
    const [currentDate, setCurrentDate] = useState(new Date());
    const [scheduleData, setScheduleData] = useState([]);
    const [queues, setQueues] = useState([]);
    const [selectedQueue, setSelectedQueue] = useState(''); // filtr – wszystkie kolejki domyślnie
    const [generationQueue, setGenerationQueue] = useState(''); // do formularza
    const [agents, setAgents] = useState([]);
    const [selectedAgents, setSelectedAgents] = useState([]);
    const [generateStartDate, setGenerateStartDate] = useState(startOfWeek(new Date(), { weekStartsOn: 1 }));
    const [generateEndDate, setGenerateEndDate] = useState(addDays(generateStartDate, 6));
    const [modalOpen, setModalOpen] = useState(false);
    const [snackbarOpen, setSnackbarOpen] = useState(false);
    const [snackbarMessage, setSnackbarMessage] = useState('');
    const [snackbarSeverity, setSnackbarSeverity] = useState('success');

    const weekStart = startOfWeek(currentDate, { weekStartsOn: 1 });
    const weekDates = useMemo(() =>
            Array.from({ length: 7 }, (_, i) => format(addDays(weekStart, i), 'yyyy-MM-dd')),
        [weekStart]
    );

    const queueColorMap = useMemo(() => {
        const map: Record<string, string> = {};
        queues.forEach((q, idx) => {
            map[q.id] = shiftColors[idx % shiftColors.length];
        });
        return map;
    }, [queues]);

    useEffect(() => {
        (async () => {
            const q = await fetchQueues();
            setQueues(q);
            setSelectedQueue(''); // wszystkie kolejki
            if (q.length) setGenerationQueue(q[0].id); // pierwsza domyślna do generowania
        })();
    }, []);

    useEffect(() => {
        const fetchData = async () => {
            const start = format(weekStart, 'yyyy-MM-dd');
            const end = format(addDays(weekStart, 6), 'yyyy-MM-dd');
            const data = await fetchShifts(start, end);
            setScheduleData(data);
            setAgents(Array.from(new Set(data.map(s => s.agent?.name))));
        };
        fetchData();
    }, [currentDate]);

    const filteredSchedule = useMemo(() => {
        return scheduleData.filter(shift =>
            (!selectedQueue || shift.queue?.id === selectedQueue) &&
            (selectedAgents.length === 0 || selectedAgents.includes(shift.agent?.name))
        );
    }, [scheduleData, selectedQueue, selectedAgents]);

    const scheduleMap = useMemo(() => {
        const map: Record<string, any[]> = {};
        for (const shift of filteredSchedule) {
            const start = new Date(shift.start);
            const end = new Date(shift.end);
            const date = format(start, 'yyyy-MM-dd');
            for (let hour = start.getHours(); hour < end.getHours(); hour++) {
                const key = `${date}-${hour}`;
                if (!map[key]) map[key] = [];
                map[key].push(shift);
            }
        }
        return map;
    }, [filteredSchedule]);

    const handleGenerateSchedule = async () => {
        try {
            const success = await generateSchedule(
                format(generateStartDate, 'yyyy-MM-dd'),
                format(generateEndDate, 'yyyy-MM-dd'),
                generationQueue
            );

            if (success) {
                const data = await fetchShifts(format(generateStartDate, 'yyyy-MM-dd'), format(generateEndDate, 'yyyy-MM-dd'));
                setScheduleData(data);
                setSnackbarMessage('Schedule generated!');
                setSnackbarSeverity('success');
            } else {
                setSnackbarMessage('Failed to generate schedule.');
                setSnackbarSeverity('error');
            }
        } catch (error) {
            console.error('Error generating schedule:', error);
            setSnackbarMessage('Error generating schedule.');
            setSnackbarSeverity('error');
        }

        setModalOpen(false);
        setSnackbarOpen(true);
    };

    return (
        <Box sx={{ p: 2, borderRadius: 2, boxShadow: 3 }}>
            <Box sx={{ display: 'inline', width: '100%', mb: 2 }}>
                <Box sx={{ display: 'inline', float: 'left', width: '20%' }}>
                    <Button variant="contained" color="primary" fullWidth onClick={() => setModalOpen(true)}>
                        Generate Schedule
                    </Button>
                </Box>
                <Box sx={{ display: 'inline', float: 'left', width: '70%' }}>
                    <Typography variant="h6" sx={{ textAlign: 'center', whiteSpace: 'nowrap' }}>
                        {format(weekStart, 'MMMM dd, yyyy')} – {format(addDays(weekStart, 6), 'MMMM dd, yyyy')}
                    </Typography>
                </Box>
                <Box sx={{ display: 'inline', float: 'right', width: '10%' }}>
                    <IconButton onClick={() => setCurrentDate(subDays(currentDate, 7))}>
                        <ArrowBackIcon />
                    </IconButton>
                    <IconButton onClick={() => setCurrentDate(addDays(currentDate, 7))}>
                        <ArrowForwardIcon />
                    </IconButton>
                </Box>
            </Box>

            <Box sx={{ display: 'flex' }}>
                <Box sx={{ display: 'inline', gap: 2, mb: 2 }}>
                    <FormControl sx={{ minWidth: 250, mr: 2 }}>
                        <InputLabel>Queue</InputLabel>
                        <Select
                            value={selectedQueue}
                            label="Queue"
                            onChange={(e) => setSelectedQueue(e.target.value)}
                        >
                            <MenuItem value="">All Queues</MenuItem>
                            {queues.map(q => (
                                <MenuItem key={q.id} value={q.id}>{q.name}</MenuItem>
                            ))}
                        </Select>
                    </FormControl>

                    <FormControl sx={{ minWidth: 250 }}>
                        <InputLabel>Agents</InputLabel>
                        <Select
                            multiple
                            value={selectedAgents}
                            onChange={(e) => setSelectedAgents(e.target.value)}
                            renderValue={(selected) => selected.join(', ')}
                        >
                            {agents.map(agent => (
                                <MenuItem key={agent} value={agent}>
                                    <Checkbox checked={selectedAgents.includes(agent)} />
                                    <ListItemText primary={agent} />
                                </MenuItem>
                            ))}
                        </Select>
                    </FormControl>
                </Box>

                <TableContainer sx={{ maxHeight: '65vh', overflowY: 'auto', flexGrow: 1 }}>
                    <Table stickyHeader>
                        <TableHead>
                            <TableRow>
                                <StyledTableCell/>
                                {weekDates.map(date => (
                                    <StyledTableCell key={date} isHighlighted={new Date(date).toDateString() === new Date().toDateString()}>
                                        {format(new Date(date), 'dd/MM E')}
                                    </StyledTableCell>
                                ))}
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {Array.from({ length: 24 }, (_, hour) => (
                                <StyledTableRow key={hour}>
                                    <StyledTableCell>{`${hour.toString().padStart(2, '0')}:00`}</StyledTableCell>
                                    {weekDates.map(date => {
                                        const key = `${date}-${hour}`;
                                        const shifts = scheduleMap[key] || [];
                                        return (
                                            <StyledTableCell key={key}>
                                                {shifts.map((shift, i) => (
                                                    <Tooltip key={i} title={`${shift.agent.name} (${shift.queue.name})`} arrow>
                                                        <Box
                                                            sx={{
                                                                mb: 0.5,
                                                                px: 1,
                                                                py: 0.5,
                                                                borderRadius: 1,
                                                                backgroundColor: queueColorMap[shift.queue?.id] || '#e0e0e0',
                                                                color: '#000',
                                                                fontSize: '0.75rem',
                                                                whiteSpace: 'nowrap',
                                                                overflow: 'hidden',
                                                                textOverflow: 'ellipsis',
                                                            }}
                                                        >
                                                            {shift.agent.name}
                                                        </Box>
                                                    </Tooltip>
                                                ))}
                                            </StyledTableCell>
                                        );
                                    })}
                                </StyledTableRow>
                            ))}
                        </TableBody>
                    </Table>
                </TableContainer>
            </Box>

            <Modal open={modalOpen} onClose={() => setModalOpen(false)}>
                <Box sx={{ p: 4, backgroundColor: 'white', borderRadius: 2, boxShadow: 24, width: 400, mx: 'auto', mt: '10%' }}>
                    <Typography variant="h6" gutterBottom>Select Schedule Dates</Typography>
                    <DatePicker
                        label="Start Date"
                        value={generateStartDate}
                        onChange={(val) => {
                            if (val instanceof Date && isValid(val)) {
                                const start = startOfWeek(val, { weekStartsOn: 1 });
                                setGenerateStartDate(start);
                                setGenerateEndDate(addDays(start, 6));
                            }
                        }}
                        format="yyyy-MM-dd"
                        slotProps={{ textField: { fullWidth: true, sx: { mb: 2 } } }}
                    />
                    <DatePicker
                        label="End Date"
                        value={generateEndDate}
                        onChange={(val) => {
                            if (val instanceof Date && isValid(val)) {
                                setGenerateEndDate(val);
                            }
                        }}
                        format="yyyy-MM-dd"
                        slotProps={{ textField: { fullWidth: true, sx: { mb: 2 } } }}
                    />
                    <FormControl fullWidth sx={{ mb: 2 }}>
                        <InputLabel>Queue</InputLabel>
                        <Select
                            value={generationQueue}
                            label="Queue"
                            onChange={(e) => setGenerationQueue(e.target.value)}
                        >
                            {queues.map(q => <MenuItem key={q.id} value={q.id}>{q.name}</MenuItem>)}
                        </Select>
                    </FormControl>
                    <Button fullWidth variant="contained" onClick={handleGenerateSchedule}>Generate</Button>
                </Box>
            </Modal>

            <Snackbar open={snackbarOpen} autoHideDuration={4000} onClose={() => setSnackbarOpen(false)}>
                <Alert severity={snackbarSeverity} onClose={() => setSnackbarOpen(false)} sx={{ width: '100%' }}>
                    {snackbarMessage}
                </Alert>
            </Snackbar>
        </Box>
    );
};

export default SchedulerTable;
