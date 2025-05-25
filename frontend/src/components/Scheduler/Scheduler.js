import React, { useState, useEffect } from 'react';
import { Container, Box, CircularProgress, Snackbar } from '@mui/material';
import { motion } from 'framer-motion';
import { fetchAgents, fetchEfficiencies, fetchQueues, fetchPredictions, fetchShifts, generateSchedule } from '../../api';
import DataGridTable from './DataGridTable';
import SchedulerTable from './SchedulerTable';
import TabsComponent from './TabsComponent';
import { GenerateButton, MainContent, BackgroundContainer } from './StyledComponents';
import './Scheduler.css';

const Scheduler = () => {
    const [selectedTab, setSelectedTab] = useState(0);
    const [data, setData] = useState({
        agents: null,
        efficiencies: null,
        queues: null,
        predictions: null,
        shifts: null,
    });
    const [loading, setLoading] = useState(false);
    const [generateLoading, setGenerateLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [openSnackbar, setOpenSnackbar] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');

    const tabData = [
        { label: 'Agents', key: 'agents', fetchData: fetchAgents },
        { label: 'Efficiencies', key: 'efficiencies', fetchData: fetchEfficiencies },
        { label: 'Queues', key: 'queues', fetchData: fetchQueues },
        { label: 'Predictions', key: 'predictions', fetchData: fetchPredictions },
        { label: 'Shifts', key: 'shifts', fetchData: fetchShifts },
        { label: 'Scheduler', key: 'scheduler' },
    ];

    useEffect(() => {
        const fetchDataForTab = async () => {
            setLoading(true);
            try {
                const selectedTabData = tabData[selectedTab];
                if (selectedTabData && !data[selectedTabData.key]) {
                    const newData = await selectedTabData.fetchData();
                    setData(prevData => ({
                        ...prevData,
                        [selectedTabData.key]: newData
                    }));
                }
            } catch (error) {
                console.error('Error fetching data for tab:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchDataForTab();
    }, [selectedTab]);

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
                    <SchedulerTable scheduleData={data.shifts || []} />
                </Box>
            );
        }

        const selectedData = data[tabData[selectedTab]?.key];

        if (!selectedData) {
            return (
                <Box display="flex" justifyContent="center" alignItems="center" height="100%">
                    <CircularProgress />
                </Box>
            );
        }

        return <DataGridTable rows={selectedData || []} />;
    };

    const handleGenerateSchedule = async () => {
        setGenerateLoading(true);
        setErrorMessage('');
        setSuccessMessage('');

        try {
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
            <TabsComponent selectedTab={selectedTab} setSelectedTab={setSelectedTab} tabData={tabData} />
            <Container maxWidth="lg" className="scheduler-container">
                <MainContent>

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
