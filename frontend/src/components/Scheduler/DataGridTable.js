import React, { useState, useMemo } from 'react';
import { DataGrid } from '@mui/x-data-grid';
import { Box, TablePagination, styled, Typography } from '@mui/material';
import { motion } from 'framer-motion';
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
        width: 200,
        sortable: true,
        filterable: true,
        hide: key === 'id',
        renderCell: (params) => {
            const value = params.value;
            if (value && typeof value === 'object') {
                return value.name || value.city || value.score || JSON.stringify(value);
            }
            return value;
        },
    }));
};

const DataGridTable = ({ rows }) => {
    const [paginationModel, setPaginationModel] = useState({ page: 0, pageSize: 10 });
    const columns = generateColumns(rows);

    const totalPages = Math.ceil(rows.length / paginationModel.pageSize);

    const paginatedRows = useMemo(() => {
        const startIdx = paginationModel.page * paginationModel.pageSize;
        const endIdx = startIdx + paginationModel.pageSize;
        return rows.slice(startIdx, endIdx);
    }, [rows, paginationModel.page, paginationModel.pageSize]);

    const handlePageChange = (event, newPage) => {
        setPaginationModel((prev) => ({ ...prev, page: newPage }));
    };

    const handlePageSizeChange = (event) => {
        setPaginationModel((prev) => ({
            ...prev,
            pageSize: parseInt(event.target.value, 10),
            page: 0,
        }));
    };

    return (
        <Box sx={{ maxWidth: '1200px', margin: 'auto' }}>
            <motion.div
                key="data-grid"
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.95 }}
                transition={{ duration: 0.5, ease: 'easeInOut' }}
            >
                {rows.length === 0 ? (
                    <Typography variant="h6" color="textSecondary" align="center" mt={3}>
                        No records
                    </Typography>
                ) : (
                    <StyledDataGrid
                        rows={paginatedRows}
                        columns={columns}
                        pagination={false}
                        disableColumnMenu
                        autoHeight
                        hideFooterSelectedRowCount
                        sx={{
                            '& .MuiTablePagination-root': {
                                display: 'none',
                            },
                        }}
                    />
                )}
            </motion.div>
            {rows.length > 0 && (
                <Box display="flex" justifyContent="flex-end" mt={2}>
                    <TablePagination
                        component="div"
                        count={rows.length}
                        page={paginationModel.page}
                        onPageChange={handlePageChange}
                        rowsPerPage={paginationModel.pageSize}
                        onRowsPerPageChange={handlePageSizeChange}
                        rowsPerPageOptions={[5, 10, 25, 50]}
                        labelRowsPerPage="Rows per page"
                        showFirstButton
                        showLastButton
                    />
                </Box>
            )}
        </Box>
    );
};

export default DataGridTable;
