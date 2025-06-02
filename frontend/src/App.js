import React from 'react';
import Scheduler from './components/Scheduler/Scheduler';
import { LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDateFns } from '@mui/x-date-pickers/AdapterDateFns';

function App() {
    return (
        <LocalizationProvider dateAdapter={AdapterDateFns}>
            <div className="App">
                <Scheduler />
            </div>
        </LocalizationProvider>
    );
}

export default App;
