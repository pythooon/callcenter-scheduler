const API_URL = process.env.REACT_APP_API_URL;

export const fetchAgents = async () => {
    const response = await fetch(`${API_URL}/api/scheduler/agents`);
    return await response.json();
};

export const fetchEfficiencies = async () => {
    const response = await fetch(`${API_URL}/api/scheduler/efficiencies`);
    return await response.json();
};

export const fetchQueues = async () => {
    const response = await fetch(`${API_URL}/api/scheduler/queues`);
    return await response.json();
};

export const fetchPredictions = async () => {
    const response = await fetch(`${API_URL}/api/scheduler/predictions`);
    return await response.json();
};

export const fetchShifts = async (start, end) => {
    let url = `${API_URL}/api/scheduler/shifts`;
    if (start && end) {
        url += `?start_date=${start}&end_date=${end}`;
    }
    const response = await fetch(url);
    return await response.json();
};

export const generateSchedule = async (startDate, endDate, queueId) => {
    try {
        const response = await fetch(`${API_URL}/api/scheduler/generate`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                start_date: startDate,
                end_date: endDate,
                queue_id: queueId,
            }),
        });
        return response.status === 204;
    } catch (error) {
        throw new Error('Error generating schedule');
    }
};
