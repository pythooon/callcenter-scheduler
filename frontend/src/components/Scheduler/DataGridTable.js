import React, { useMemo, useEffect, useState } from 'react';
import { DataGrid } from '@mui/x-data-grid';
import { Box, TablePagination, styled, Typography, TextField } from '@mui/material';
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

const flattenObject = (obj, prefix = '') => {
    return Object.entries(obj).reduce((acc, [key, value]) => {
        const newKey = prefix ? `${prefix}.${key}` : key;
        if (value && typeof value === 'object' && !Array.isArray(value)) {
            Object.assign(acc, flattenObject(value, newKey));
        } else {
            acc[newKey] = value;
        }
        return acc;
    }, {});
};

const formatHeaderName = (key) => {
    return key
        .split('.')
        .filter(k => k !== 'id')
        .map(k => k.charAt(0).toUpperCase() + k.slice(1))
        .join(' ');
};

const generateColumns = (data) => {
    if (data.length === 0) return [];

    const firstRow = flattenObject(data[0]);

    return Object.keys(firstRow)
        .filter((key) => key !== 'id' && !key.endsWith('.id'))
        .map((key) => ({
            field: key,
            headerName: formatHeaderName(key),
            width: 200,
            sortable: true,
            filterable: true,
            renderCell: (params) => {
                const value = params.value;
                if (value && typeof value === 'object') {
                    return value.name || JSON.stringify(value);
                }
                return value ?? '';
            },
        }));
};

const DataGridTable = ({ rows = [] }) => {
    const [paginationModel, setPaginationModel] = useState({ page: 0, pageSize: 10 });
    const [searchTerm, setSearchTerm] = useState('');

    const flatRows = useMemo(
        () =>
            rows.map((row, index) => {
                const flat = flattenObject(row);
                return {
                    id: row.id ?? index,
                    ...flat,
                };
            }),
        [rows]
    );

    const columns = useMemo(() => generateColumns(flatRows), [flatRows]);

    // 🔍 Filtrowanie po tekście
    const filteredRows = useMemo(() => {
        if (!searchTerm.trim()) return flatRows;

        const lowerTerm = searchTerm.toLowerCase();

        return flatRows.filter(row =>
            Object.values(row).some(value =>
                String(value).toLowerCase().includes(lowerTerm)
            )
        );
    }, [flatRows, searchTerm]);

    const totalRows = filteredRows.length;
    const totalPages = Math.ceil(totalRows / paginationModel.pageSize);

    useEffect(() => {
        if (paginationModel.page >= totalPages) {
            setPaginationModel((prev) => ({ ...prev, page: 0 }));
        }
    }, [paginationModel.page, paginationModel.pageSize, totalPages]);

    const handlePageChange = (event, newPage) => {
        setPaginationModel((prev) => ({ ...prev, page: newPage }));
    };

    const handlePageSizeChange = (event) => {
        setPaginationModel({
            pageSize: parseInt(event.target.value, 10),
            page: 0,
        });
    };

    const paginatedRows = useMemo(() => {
        const start = paginationModel.page * paginationModel.pageSize;
        const end = start + paginationModel.pageSize;
        return filteredRows.slice(start, end);
    }, [filteredRows, paginationModel]);

    return (
        <Box sx={{ maxWidth: '1200px', margin: 'auto' }}>
            <Box mt={2} mb={2}>
                <TextField
                    fullWidth
                    label="Search"
                    variant="outlined"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                />
            </Box>

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
                        pageSize={paginationModel.pageSize}
                        pagination={false}
                        disableColumnMenu
                        autoHeight
                        hideFooter
                        sx={{
                            '& .MuiDataGrid-virtualScroller': {
                                overflow: 'auto !important',
                            },
                        }}
                    />
                )}
            </motion.div>

            {filteredRows.length > 0 && (
                <Box display="flex" justifyContent="flex-end" mt={2}>
                    <TablePagination
                        component="div"
                        count={filteredRows.length}
                        page={paginationModel.page}
                        onPageChange={handlePageChange}
                        rowsPerPage={paginationModel.pageSize}
                        onRowsPerPageChange={handlePageSizeChange}
                        rowsPerPageOptions={[5, 10, 25, 50]}
                        labelRowsPerPage="Records per page"
                        showFirstButton
                        showLastButton
                    />
                </Box>
            )}
        </Box>
    );
};

export default DataGridTable;
