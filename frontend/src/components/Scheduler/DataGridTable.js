import React, { useState, useMemo } from 'react';
import { DataGrid } from '@mui/x-data-grid';
import { styled } from '@mui/system';
import './DataGridTable.css';

const StyledDataGrid = styled(DataGrid)(({ theme }) => ({
    '& .MuiDataGrid-columnHeader': {
        fontWeight: 'bold',
        backgroundColor: theme.palette.background.default,
    },
    '& .MuiDataGrid-cell': {
        color: theme.palette.text.primary,
    },
    boxShadow: '0 4px 10px rgba(0, 0, 0, 0.1)',
    borderRadius: '8px',
}));

const generateColumns = (data) => {
    if (data.length === 0) return [];

    const keys = Object.keys(data[0]);
    return keys.map((key) => ({
        field: key,
        headerName: key.charAt(0).toUpperCase() + key.slice(1),
        width: 250,
        sortable: true,
        renderCell: (params) => {
            return typeof params.value === 'object' ? params.value.name : params.value;
        }
    }));
};

const DataGridTable = ({ rows }) => {
    const [paginationModel, setPaginationModel] = useState({
        page: 0,
        pageSize: 10,
    });

    const columns = generateColumns(rows);

    const paginatedRows = useMemo(() => {
        const startIdx = paginationModel.page * paginationModel.pageSize;
        const endIdx = startIdx + paginationModel.pageSize;
        return rows.slice(startIdx, endIdx);
    }, [rows, paginationModel.page, paginationModel.pageSize]);

    return (
        <div className="data-grid-container">
            <StyledDataGrid
                rows={paginatedRows}
                columns={columns}
                pageSize={paginationModel.pageSize}
                paginationModel={paginationModel}
                onPaginationModelChange={setPaginationModel}
                pagination
                disableColumnMenu
                autoHeight
            />
        </div>
    );
};

export default DataGridTable;
