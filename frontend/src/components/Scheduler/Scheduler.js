import React, { useState, useEffect } from 'react';
import { Container, Box, CircularProgress, Snackbar } from '@mui/material';
import { motion } from 'framer-motion';
import { fetchAgents, fetchEfficiencies, fetchQueues, fetchPredictions, fetchShifts, calculateEfficiency, generateSchedule } from '../../api';
import DataGridTable from './DataGridTable';
import SchedulerTable from './SchedulerTable';
import TabsComponent from './TabsComponent';
import { GenerateButton, MainContent, BackgroundContainer } from './StyledComponents';
import './Scheduler.css';

const Scheduler = () => {
    const [selectedTab, setSelectedTab] = useState(0);
    const [data, setData] = useState({
        agents: [],
        efficiencies: [],
        queues: [],
        predictions: [],
        shifts: [],
    });
    const [loading, setLoading] = useState(true);
    const [generateLoading, setGenerateLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [openSnackbar, setOpenSnackbar] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            try {
                const agentsData = await fetchAgents();
                const efficienciesData = await fetchEfficiencies();
                const queuesData = await fetchQueues();
                const predictionsData = await fetchPredictions();
                const shiftsData = await fetchShifts();

                setData({
                    agents: agentsData,
                    efficiencies: efficienciesData,
                    queues: queuesData,
                    predictions: predictionsData,
                    shifts: shiftsData,
                });
            } catch (error) {
                console.error('Error fetching data:', error);
            } finally {
                setLoading(false);
            }
        };
        fetchData();
    }, []);

    const tabData = [
        { label: 'Agents', key: 'agents' },
        { label: 'Efficiencies', key: 'efficiencies' },
        { label: 'Queues', key: 'queues' },
        { label: 'Predictions', key: 'predictions' },
        { label: 'Shifts', key: 'shifts' },
        { label: 'Scheduler', key: 'scheduler' },
    ];

    const renderTabContent = () => {
        if (loading) {
            return (
                <Box display="flex" justifyContent="center" alignItems="center" height="100%">
                    <CircularProgress />
                </Box>
            );
        }

        if (selectedTab === 5) {
            return (
                <Box display="flex" justifyContent="center" alignItems="center" height="100%">
                    <SchedulerTable scheduleData={data.shifts} />
                </Box>
            );
        }

        const selectedData = data[tabData[selectedTab]?.key];
        return <DataGridTable rows={selectedData} />;
    };

    const handleGenerateSchedule = async () => {
        setGenerateLoading(true);
        setErrorMessage('');
        setSuccessMessage('');

        try {
            await calculateEfficiency();
            await generateSchedule();
            setSuccessMessage('Schedule generated successfully!');
            setOpenSnackbar(true);
        } catch (error) {
            setErrorMessage('Failed to generate schedule');
            setOpenSnackbar(true);
        } finally {
            setGenerateLoading(false);
        }
    };

    return (
        <div className="scheduler">
            <BackgroundContainer />
            <Container maxWidth="lg" className="scheduler-container">
                <MainContent>
                    <TabsComponent selectedTab={selectedTab} setSelectedTab={setSelectedTab} tabData={tabData} />
                    <Box className="content-container">
                        <motion.div
                            key={selectedTab}
                            initial={{ opacity: 0, scale: 0.8 }}
                            animate={{ opacity: 1, scale: 1 }}
                            exit={{ opacity: 0, scale: 0.8 }}
                            transition={{ duration: 0.5, ease: 'easeInOut' }}
                        >
                            {renderTabContent()}
                        </motion.div>
                    </Box>

                    {selectedTab === 5 && (
                        <Box display="flex" justifyContent="center" className="generate-btn-container">
                            <GenerateButton
                                variant="contained"
                                onClick={handleGenerateSchedule}
                                disabled={generateLoading}
                            >
                                {generateLoading ? 'Generating...' : 'Generate Schedule'}
                            </GenerateButton>
                        </Box>
                    )}
                </MainContent>
            </Container>

            <Snackbar
                open={openSnackbar}
                autoHideDuration={6000}
                onClose={() => setOpenSnackbar(false)}
                message={successMessage || errorMessage}
            />
        </div>
    );
};

export default Scheduler;
