import React, { useState, useMemo, useEffect } from 'react';
import { Box, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Typography, IconButton, Tooltip } from '@mui/material';
import { styled } from '@mui/system';
import { format, addDays, startOfWeek, subDays } from 'date-fns';
import ArrowForwardIcon from '@mui/icons-material/ArrowForward';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import { fetchShifts } from '../../api';

const StyledTableCell = styled(TableCell)({
    padding: '12px',
    textAlign: 'center',
    verticalAlign: 'middle',
    minWidth: '120px',
    height: '80px',
    width: '120px',
    border: '1px solid #ddd',
    transition: 'background-color 0.3s, transform 0.3s',
    '&:hover': {
        backgroundColor: '#f0f0f0',
        transform: 'scale(1.05)',
    },
});

const StyledTableRow = styled(TableRow)({
    '&:nth-of-type(odd)': {
        backgroundColor: '#f9f9f9',
    },
    '&:hover': {
        backgroundColor: '#f1f1f1',
    },
});

const CustomTooltip = styled(Tooltip)({
    '& .MuiTooltip-tooltip': {
        backgroundColor: '#333',
        color: '#fff',
        fontSize: '14px',
        borderRadius: '4px',
        padding: '10px',
        maxWidth: '300px',
        whiteSpace: 'normal',
        wordBreak: 'break-word',
        boxShadow: '0px 4px 6px rgba(0, 0, 0, 0.1)',
    },
});

const SchedulerTable = () => {
    const [currentDate, setCurrentDate] = useState(new Date());
    const [scheduleData, setScheduleData] = useState([]);
    const [isLoading, setIsLoading] = useState(false);

    const daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const hoursOfDay = Array.from({ length: 24 }, (_, i) => `${i}:00`);

    const getWeekDates = useMemo(() => {
        const startOfCurrentWeek = startOfWeek(currentDate, { weekStartsOn: 1 });
        const weekDates = [];
        for (let i = 0; i < 7; i++) {
            const day = addDays(startOfCurrentWeek, i);
            weekDates.push(format(day, 'yyyy-MM-dd'));
        }
        return weekDates;
    }, [currentDate]);

    const fetchScheduleData = async (startDate, endDate) => {
        setIsLoading(true);
        try {
            const data = await fetchShifts(startDate, endDate);
            setScheduleData(data);
        } catch (error) {
            console.error('Error fetching schedule data:', error);
        } finally {
            setIsLoading(false);
        }
    };

    const handlePrevWeek = () => {
        const newDate = subDays(currentDate, 7);
        setCurrentDate(newDate);
        const startOfWeekDate = format(startOfWeek(newDate, { weekStartsOn: 1 }), 'yyyy-MM-dd');
        const endOfWeekDate = format(addDays(startOfWeek(newDate, { weekStartsOn: 1 }), 6), 'yyyy-MM-dd');
        fetchScheduleData(startOfWeekDate, endOfWeekDate);
    };

    const handleNextWeek = () => {
        const newDate = addDays(currentDate, 7);
        setCurrentDate(newDate);
        const startOfWeekDate = format(startOfWeek(newDate, { weekStartsOn: 1 }), 'yyyy-MM-dd');
        const endOfWeekDate = format(addDays(startOfWeek(newDate, { weekStartsOn: 1 }), 6), 'yyyy-MM-dd');
        fetchScheduleData(startOfWeekDate, endOfWeekDate);
    };

    useEffect(() => {
        const startOfWeekDate = format(startOfWeek(currentDate, { weekStartsOn: 1 }), 'yyyy-MM-dd');
        const endOfWeekDate = format(addDays(startOfWeek(currentDate, { weekStartsOn: 1 }), 6), 'yyyy-MM-dd');
        fetchScheduleData(startOfWeekDate, endOfWeekDate);
    }, [currentDate]);

    const getRowContent = (dayIndex, hour, weekDate) => {
        if (!weekDate) return null;

        const tasksForThisHour = scheduleData.filter((shift) => {
            const taskStart = new Date(shift.start);
            const taskEnd = new Date(shift.end);
            const currentDay = new Date(weekDate);
            const currentHourStart = new Date(currentDay.setHours(hour, 0, 0, 0));
            const currentHourEnd = new Date(currentDay.setHours(hour + 1, 0, 0, 0));

            return taskStart < currentHourEnd && taskEnd > currentHourStart;
        });

        if (tasksForThisHour.length > 0) {
            return tasksForThisHour.map((task, index) => {
                const taskStart = new Date(task.start);
                const taskEnd = new Date(task.end);
                return (
                    <CustomTooltip
                        key={index}
                        title={`
                            Agent: ${task.agent.name} | 
                            Queue: ${task.queue.name} | 
                            Time: ${format(taskStart, 'HH:mm')} - ${format(taskEnd, 'HH:mm')}
                        `}
                        placement="top"
                        arrow
                    >
                        <Box
                            sx={{
                                backgroundColor: '#c5e1a5',
                                padding: '6px',
                                borderRadius: '4px',
                                cursor: 'pointer',
                                boxShadow: '0 4px 8px rgba(0, 0, 0, 0.1)',
                                position: 'relative',
                            }}
                        >
                            <Typography variant="body2" color="textPrimary">{task.agent.name}</Typography>
                            <Typography variant="body2" color="textSecondary">{task.queue.name}</Typography>
                        </Box>
                    </CustomTooltip>
                );
            });
        }

        return null;
    };

    return (
        <Box sx={{ boxShadow: '0 4px 15px rgba(0, 0, 0, 0.1)', borderRadius: '8px', padding: '16px', marginBottom: '10px' }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '10px' }}>
                <Typography variant="h6" sx={{ textAlign: 'center', marginX: '10px' }}>
                    {format(currentDate, 'MMMM dd, yyyy')} - {format(addDays(currentDate, 6), 'MMMM dd, yyyy')}
                </Typography>
                <Box className="inline-buttons">
                    <IconButton onClick={handlePrevWeek}>
                        <ArrowBackIcon />
                    </IconButton>
                    <IconButton onClick={handleNextWeek}>
                        <ArrowForwardIcon />
                    </IconButton>
                </Box>
            </Box>

            {isLoading ? (
                <Typography variant="h6" sx={{ textAlign: 'center' }}>
                    Loading...
                </Typography>
            ) : (
                <TableContainer sx={{ maxHeight: '80vh', overflowY: 'auto' }}>
                    <Table stickyHeader>
                        <TableHead>
                            <TableRow>
                                <StyledTableCell />
                                {daysOfWeek.map((day, index) => (
                                    <StyledTableCell key={index}>
                                        {day} ({getWeekDates[index]})
                                    </StyledTableCell>
                                ))}
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            {hoursOfDay.map((hour, index) => (
                                <StyledTableRow key={index}>
                                    <StyledTableCell>{hour}</StyledTableCell>
                                    {daysOfWeek.map((day, dayIndex) => (
                                        <StyledTableCell key={dayIndex}>
                                            {getRowContent(dayIndex, index, getWeekDates[dayIndex])}
                                        </StyledTableCell>
                                    ))}
                                </StyledTableRow>
                            ))}
                        </TableBody>
                    </Table>
                </TableContainer>
            )}
        </Box>
    );
};

export default SchedulerTable;
