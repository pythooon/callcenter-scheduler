import React, { useState } from 'react';
import { Box, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Typography } from '@mui/material';
import { styled } from '@mui/system';
import { format, addDays, startOfWeek } from 'date-fns';

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

const getNextWeekDates = () => {
    const today = new Date();
    const startOfNextWeek = startOfWeek(addDays(today, 7), { weekStartsOn: 1 });
    const weekDates = [];

    for (let i = 0; i < 7; i++) {
        const day = addDays(startOfNextWeek, i);
        weekDates.push(format(day, 'yyyy-MM-dd'));
    }

    return weekDates;
};

const SchedulerTable = ({ scheduleData }) => {
    const [tooltipInfo, setTooltipInfo] = useState(null);
    const [tooltipPosition, setTooltipPosition] = useState({ x: 0, y: 0 });

    const daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const weekDates = getNextWeekDates();
    const hoursOfDay = Array.from({ length: 24 }, (_, i) => `${i}:00`);

    const getRowContent = (dayIndex, hour, weekDate) => {
        if (!weekDate) return null;

        const task = scheduleData.find((shift) => {
            const taskStart = new Date(shift.start);
            const taskEnd = new Date(shift.end);
            const currentDay = new Date(weekDate);
            const currentHourStart = new Date(currentDay.setHours(hour, 0, 0, 0));
            const currentHourEnd = new Date(currentDay.setHours(hour + 1, 0, 0, 0));

            return taskStart < currentHourEnd && taskEnd > currentHourStart;
        });

        if (task) {
            return (
                <Box
                    sx={{
                        backgroundColor: '#c5e1a5',
                        padding: '6px',
                        borderRadius: '4px',
                        cursor: 'pointer',
                        boxShadow: '0 4px 8px rgba(0, 0, 0, 0.1)',
                    }}
                    onMouseEnter={(e) => handleMouseEnter(e, task)}
                    onMouseLeave={handleMouseLeave}
                >
                    <Typography variant="body2" color="textPrimary">{task.agent.name}</Typography>
                    <Typography variant="body2" color="textSecondary">{task.queue.name}</Typography>
                </Box>
            );
        }

        return null;
    };

    const handleMouseEnter = (e, task) => {
        const tooltipData = `${task.agent.name} - ${task.queue.name}: ${task.start} - ${task.end}`;
        const rect = e.target.getBoundingClientRect();
        setTooltipInfo(tooltipData);
        setTooltipPosition({
            x: rect.left + window.scrollX + 10,
            y: rect.top + window.scrollY - 30,
        });
    };

    const handleMouseLeave = () => {
        setTooltipInfo(null);
    };

    return (
        <Box className="scheduler-table-container" sx={{ boxShadow: '0 4px 15px rgba(0, 0, 0, 0.1)', borderRadius: '8px', padding: '16px' }}>
            <TableContainer sx={{ maxHeight: '80vh', overflowY: 'auto' }}>
                <Table stickyHeader>
                    <TableHead>
                        <TableRow>
                            <StyledTableCell />
                            {daysOfWeek.map((day, index) => (
                                <StyledTableCell key={index}>
                                    {day} ({weekDates[index]})
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
                                        {getRowContent(dayIndex, index, weekDates[dayIndex])}
                                    </StyledTableCell>
                                ))}
                            </StyledTableRow>
                        ))}
                    </TableBody>
                </Table>
            </TableContainer>

            {tooltipInfo && (
                <Box
                    sx={{
                        position: 'absolute',
                        top: tooltipPosition.y,
                        left: tooltipPosition.x,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        color: 'white',
                        padding: '8px',
                        borderRadius: '5px',
                        zIndex: 10,
                        fontSize: '12px',
                        whiteSpace: 'nowrap',
                    }}
                >
                    {tooltipInfo}
                </Box>
            )}
        </Box>
    );
};

export default SchedulerTable;
