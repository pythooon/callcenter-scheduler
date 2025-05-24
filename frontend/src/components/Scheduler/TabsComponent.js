import React from 'react';
import { Tabs } from '@mui/material';
import { TabButton } from './StyledComponents';

const TabsComponent = ({ selectedTab, setSelectedTab, tabData }) => {
    const handleTabChange = (event, newValue) => {
        setSelectedTab(newValue);
    };

    return (
        <div className="tabs">
            <Tabs value={selectedTab} onChange={handleTabChange} centered indicatorColor="secondary">
                {tabData.map((tab, index) => (
                    <TabButton key={index} label={tab.label} value={index} />
                ))}
            </Tabs>
        </div>
    );
};

export default TabsComponent;
