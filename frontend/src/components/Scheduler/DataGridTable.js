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

const DataGridTable = ({ rows = [] }) => {
    const [paginationModel, setPaginationModel] = useState({ page: 0, pageSize: 10 });
    const columns = useMemo(() => generateColumns(rows), [rows]);

    const totalRows = rows.length;
    const totalPages = Math.ceil(totalRows / paginationModel.pageSize);

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
            page: 0, // Resetujemy stronę na 0 przy zmianie liczby rekordów
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
                        Brak rekordów
                    </Typography>
                ) : (
                    <StyledDataGrid
                        rows={paginatedRows}
                        columns={columns}
                        pageSize={paginationModel.pageSize}
                        pagination
                        page={paginationModel.page}
                        onPageChange={handlePageChange}
                        rowsPerPageOptions={[5, 10, 25, 50]}
                        disableColumnMenu
                        autoHeight
                        hideFooterSelectedRowCount
                    />
                )}
            </motion.div>

            {rows.length > 0 && (
                <Box display="flex" justifyContent="flex-end" mt={2}>
                    <TablePagination
                        component="div"
                        count={totalRows}
                        page={paginationModel.page}
                        onPageChange={handlePageChange}
                        rowsPerPage={paginationModel.pageSize}
                        onRowsPerPageChange={handlePageSizeChange}
                        rowsPerPageOptions={[5, 10, 25, 50]}
                        labelRowsPerPage="Rekordów na stronę"
                        showFirstButton
                        showLastButton
                        pageCount={totalPages}
                    />
                </Box>
            )}
        </Box>
    );
};

export default DataGridTable;
