import { Button, Box, Typography, Tab } from '@mui/material';
import { styled } from '@mui/system';

export const GenerateButton = styled(Button)({
    marginTop: '16px',
    padding: '12px 24px',
    fontSize: '16px',
});

export const TabButton = styled(Tab)({
    textTransform: 'none',
    fontSize: '16px',
    fontWeight: 600,
});

export const MainContent = styled(Box)({
    padding: '24px',
    width: '100%',
    minHeight: '80vh',
    boxShadow: '0 4px 10px rgba(0, 0, 0, 0.1)',
    borderRadius: '8px',
});

export const BackgroundContainer = styled(Box)({
    position: 'absolute',
    top: 0,
    left: 0,
    width: '100%',
    height: '100%',
    backgroundColor: '#f5f5f5',
});
