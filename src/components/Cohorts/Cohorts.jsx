import React, { useState, useEffect } from "react";
import axios from "axios";
import { getApiLink } from "../../services/apiService";
import CustomTable, {
    TableCell,
} from "../AdminLibrary/CustomTable/CustomTable";
import Banner from "../Banner/banner";
import { useRef } from "react";
import './cohorts.scss';
import Propopup from "../PopupContent/PopupContent";
import Dialog from "@mui/material/Dialog";
import defaultImage from '../../assets/images/moowoodle-product-default.png';

export default function Cohorts() {
    const { __ } = wp.i18n;
    const [data, setData] = useState(null);
    const [cohorts, setCohorts] = useState([]);
    const [products, setProducts] = useState([]);
    const [selectedRows, setSelectedRows] = useState([]);
    const [totalRows, setTotalRows] = useState();
    const bulkSelectRef = useRef();
    const [openDialog, setOpenDialog] = useState(false);

    useEffect(() => {
        axios({
            method: "get",
            url: getApiLink('all-cohorts'),
            headers: { "X-WP-Nonce": appLocalizer.nonce },
        }).then((response) => {
            setCohorts(response.data.cohorts);
            setProducts(response.data.products);
        });
    }, []);

    /**
     * Function that request data from backend
     * @param {*} rowsPerPage 
     * @param {*} currentPage 
     */
    function requestData(
        rowsPerPage = 10,
        currentPage = 1,
        courseField = '',
        productField = '',
        catagoryField = '',
        searchAction = '',
        searchCourseField = ''
    ) {
        //Fetch the data to show in the table
        axios({
            method: "post",
            url: getApiLink('get-cohorts'),
            headers: { "X-WP-Nonce": appLocalizer.nonce },
            data: {
                page: currentPage,
                row: rowsPerPage,
                product: productField,
                searchaction: searchAction,
                search: searchCourseField
            },
        }).then((response) => {
            setData(response.data);
        });
    }

    /**
     * Callback function for request data from rest api.
     * @param {*} rowsPerPage 
     * @param {*} currentPage 
     * @param {*} filterData 
     */
    const requestApiForData = (rowsPerPage, currentPage, filterData = {}) => {
        
        // If serch action or search text fields any one of is missing then do nothing 
        if ( Boolean( filterData?.searchAction ) ^ Boolean( filterData?.searchCourseField ) ) {
            return;
        }

        setData(null);

        requestData(
            rowsPerPage,
            currentPage,
            filterData?.courseField,
            filterData?.productField,
            filterData?.catagoryField,
            filterData?.searchAction,
            filterData?.searchCourseField
        );
    };

    /**
     * Handle single row action
     * @param {*} actionName 
     * @param {*} courseId 
     * @param {*} rowId 
     * @param {*} rowIndex 
     */
    const handleSingleAction = (actionName, courseId, moodleCourseId) => {
        if (appLocalizer.khali_dabba) {
            setData(null);
            axios({
                method: 'post',
                url: getApiLink(`course-bulk-action`),
                headers: { 'X-WP-Nonce': appLocalizer.nonce },
                data: {
                    selected_action: actionName,
                    course_ids: [{
                        course_id: courseId,
                        moodle_course_id: moodleCourseId,
                    }]
                },
            }).then((response) => {
                // Handle after single row action success.
                requestData()
            }).catch((error) => {
                console.error('Error:', error);
            });
        } else {
            setOpenDialog(true);
        }
    }

    const handleBulkAction = (event) => {
        if (appLocalizer.khali_dabba) {
            if (!selectedRows.length) {
                return window.alert(__('Select rows', 'moowoodle'));
            }
            if (!bulkSelectRef.current.value) {
                return window.alert(__('Select bulk action', 'moowoodle'));
            }
            setData(null);
            axios({
                method: 'post',
                url: getApiLink(`course-bulk-action`),
                headers: { 'X-WP-Nonce': appLocalizer.nonce },
                data: {
                    selected_action: bulkSelectRef.current.value,
                    course_ids: selectedRows.map((row) => {
                        return { course_id: row.id, moodle_course_id: row.moodle_course_id }
                    })
                },
            }).then((response) => {
                // handle after bulk action success
                requestData();
            }).catch((error) => {
                console.error('Error:', error);
            });
        } else {
            setOpenDialog(true);
        }
    }

    const handleRowSelect = (selectedRows, selectedCount, allSelect) => {
        setSelectedRows(selectedRows);
    }

    // Get the total no of data present in database
    useEffect(() => {
        axios({
            method: "post",
            url: getApiLink('get-cohorts'),
            headers: { "X-WP-Nonce": appLocalizer.nonce },
            data: { count: true },
        }).then((response) => {
            setTotalRows(response.data);
        });
    }, []);

    // Get the initial data for render
    useEffect(() => {
        requestData();
    }, []);

    //columns for the data table
    const columns = [
        {
            name: __('Cohorts', 'moowoodle'),
            selector: row => row.cohort_name,
            cell: (row) => (
                <TableCell>
                    <img src={row.productimage || defaultImage} />
                    <div className="action-section">
                        <p>{row.cohort_name}</p>
                        <div className='action-btn'>
                            <a target='_blank' href={row.moodle_url} className="">Edit cohort</a>
                        </div>
                    </div>
                </TableCell>
            ),
            sortable: true,
        },
         {
            name: __('Product', 'moowoodle'),
            selector: row => row.products,
            cell: (row) => (
                <TableCell title={'Product Name'}>
                    {
                        Object.keys(row.products).length ? (
                            Object.entries(row.products).map(([name, url], index) => {
                                return (
                                    <>
                                        <div key={index} className="action-section">
                                            <p>{name}</p>
                                            <div className='action-btn'>
                                                <a target='_blank' href={url} className="">Edit product</a>
                                            </div>
                                        </div>
                                    </>
                                )
                            })
                        ) : (
                            "-"
                        )
                    }
                </TableCell>
            ),
        },
        {
            name: __('Enrolled users', 'moowoodle'),
            selector: row => row.enroled_user,
            cell: (row) => (
                <TableCell title={'Enrolled users'}>
                    <div className="action-section">
                        <p>{row.enroled_user}</p>
                        <div className='action-btn'>
                            <a target='_blank' href={row.view_users_url} className="">View users</a>
                        </div>
                    </div>
                </TableCell>
            ),
            sortable: true,
        },
        {
            name: <div className="table-action-column">{__('Action', 'moowoodle')}{!appLocalizer.khali_dabba && <span className="admin-pro-tag">pro</span>}</div>,
            cell: (row, rowIndex) => (
                <div class="moowoodle-course-actions">
                    <button
                        class={`sync-single-course button-primary`}
                        title={__('Sync course data')}
                        onClick={(e) => {
                            handleSingleAction(
                                'sync_courses',
                                row.id,
                                row.moodle_course_id,
                            )
                        }}
                    >
                        <i class="dashicons dashicons-update"></i>
                    </button>
                    {
                        Object.keys(row.products).length ?
                            <button
                                class={`update-existed-single-product button-secondary `}
                                title={__('Sync Course Data & Update Product')}
                                onClick={(e) => {
                                    handleSingleAction(
                                        'update_product',
                                        row.id,
                                        row.moodle_course_id,
                                    )
                                }}
                            >
                                <i class="dashicons dashicons-admin-links"></i>
                            </button>
                            :
                            <button
                                class={`create-single-product button-secondary`}
                                title={__('Create Product')}
                                onClick={(e) => {
                                    handleSingleAction(
                                        'create_product',
                                        row.id,
                                        row.moodle_course_id,
                                    )
                                }}
                            >
                                <i class="dashicons dashicons-cloud-upload"></i>
                            </button>
                    }
                </div>
            ),
        },
    ];

    const realtimeFilter = [
        {
            name: "bulk-action",
            render: () => {
                return (
                    <div className="course-bulk-action bulk-action">
                        <select name="action" ref={bulkSelectRef} >
                            <option value="">{__('Bulk actions')}</option>
                            <option value="sync_courses">{__('Sync course')}</option>
                            <option value="create_product">{__('Create product')}</option>
                            <option value="update_product">{__('Update product')}</option>
                        </select>
                        {!appLocalizer.khali_dabba && <span className="admin-pro-tag">pro</span>}
                        <button
                            name="bulk-action-apply"
                            onClick={handleBulkAction}
                        >
                            {__('Apply',)}
                        </button>
                    </div>
                );
            },
        },
        {
            name: "searchCourseField",
            render: (updateFilter, filterValue) => (
                <>
                    <div className="admin-header-search-section searchCourseField">
                        <input
                            name="searchCourseField"
                            type="text"
                            placeholder={__("Search...", "moowoodle")}
                            onChange={(e) => updateFilter(e.target.name, e.target.value)}
                            value={filterValue || ""}
                        />
                    </div>
                </>
            ),
        },
        {
            name: "searchAction",
            render: (updateFilter, filterValue) => {
                return (
                    <>
                        <div className="admin-header-search-section searchAction">
                            <select
                                name="searchAction"
                                onChange={(e) => updateFilter(e.target.name, e.target.value)}
                                value={filterValue || ""}
                            >
                                <option value="" style={{textAlign:'center'}}>Select</option>
                                <option value="cohort">Cohort</option>
                            </select>
                        </div>
                    </>
                );
            },
        },
    ];

    return (
        openDialog ?
            (
                <Dialog
                    className="admin-module-popup"
                    open={openDialog}
                    onClose={() => {
                        setOpenDialog(false);
                    }}
                    aria-labelledby="form-dialog-title"
                >
                    <span
                        className="admin-font adminLib-cross stock-manager-popup-cross"
                        onClick={() => {
                            setOpenDialog(false);
                        }}
                    ></span>
                    <Propopup />
                </Dialog>
            )
            :
            (
                <div className="course-container-wrapper">
                    <div className="admin-page-title">
                        <p>{__("Cohorts", "moowoodle")}</p>
                    </div>

                    <div className="admin-table-wrapper">
                        {
                            <CustomTable
                                data={data}
                                columns={columns}
                                handlePagination={requestApiForData}
                                defaultRowsParPage={10}
                                defaultTotalRows={totalRows}
                                perPageOption={[10, 25, 50]}
                                selectable={true}
                                handleSelect={handleRowSelect}
                                realtimeFilter={realtimeFilter}
                                autoLoading={false}
                            />
                        }
                    </div>
                </div>
            )
    );
}
