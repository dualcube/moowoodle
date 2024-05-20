import React, { useState, useEffect } from "react";
import axios from "axios";
import { getApiLink } from "../../services/apiService";
import CustomTable, {
    TableCell,
} from "../AdminLibrary/CustomTable/CustomTable";
import Banner from "../Banner/banner";
import { useRef } from "react";
import Propopup from "../PopupContent/PopupContent";
import Dialog from "@mui/material/Dialog";
import { render } from "react-dom";
import './courses.scss';

export default function Course() {
    const { __ } = wp.i18n;
    const [data, setData] = useState(null);
    const [courses, setCourses] = useState([]);
    const [products, setProducts] = useState([]);
    const [category, setCategory] = useState([]);
    const [shortName, setShortName] = useState([]);
    const [selectedRows, setSelectedRows] = useState([]);
    const [totalRows, setTotalRows] = useState();
    const bulkSelectRef = useRef();
    const [openDialog, setOpenDialog] = useState(false);

    useEffect(() => {
        axios({
            method: "get",
            url: getApiLink('all-courses'),
        }).then((response) => {
            setCourses(response.data.courses);
            setProducts(response.data.products);
            setCategory(response.data.category);
            setShortName(response.data.shortname);
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
        shortnameField = '',
        searchCourseField =''
    ) {
        //Fetch the data to show in the table
        axios({
            method: "post",
            url: getApiLink('get-courses'),
            headers: { "X-WP-Nonce": appLocalizer.nonce },
            data: {
                page: currentPage,
                row: rowsPerPage,
                course: courseField,
                product: productField,
                catagory: catagoryField,
                shortname: shortnameField,
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
        requestData(
            rowsPerPage,
            currentPage,
            filterData?.courseField,
            filterData?.productField,
            filterData?.catagoryField,
            filterData?.shortnameField,
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
        if (appLocalizer.pro_active) {
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
                console.log(response);
            }).catch((error) => {
                console.error('Error:', error);
            });
        } else {
            setOpenDialog(true);
        }
    }

    const handleBulkAction = (event) => {
        if (appLocalizer.pro_active) {
            if (!selectedRows.length) {
                return window.alert(__('Select rows', 'moowoodle'));
            }
            if (!bulkSelectRef.current.value) {
                return window.alert(__('Select bulk action', 'moowoodle'));
            }

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
                console.log(response);
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
            url: getApiLink('get-courses'),
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
            name: __('Course Name', 'moowoodle'),
            selector: row => row.course_name,
            cell: (row) => (
                <TableCell>
                    <a href={row.moodle_url} alt="moowoodle_url">
                        {row.course_name}
                    </a>
                </TableCell>
            ),
            sortable: true,
        },
        {
            name: __('Short Name', 'moowoodle'),
            selector: row => row.course_short_name,
            cell: (row) => (
                <TableCell>
                    {row.course_short_name}
                </TableCell>
            ),
            sortable: true,
        },
        {
            name: __('Product Name', 'moowoodle'),
            selector: row => row.products,
            cell: (row) => (
                <TableCell>
                    {
                        Object.keys(row.products).length ? (
                            Object.entries(row.products).map(([name, url], index) => {
                                return <a key={index} href={url}> {name} </a>
                            })
                        ) : (
                            "-"
                        )
                    }
                </TableCell>
            ),
        },
        {
            name: __('Category Name', 'moowoodle'),
            selector: row => row.category_name,
            cell: (row) => (
                <TableCell title={'Category Name'}>
                    <a href={row.category_url} alt="category_url">
                        {row.category_name}
                    </a>
                </TableCell>
            ),
            sortable: true,
        },
        {
            name: __('Enrolled Users', 'moowoodle'),
            cell: (row) => (
                <TableCell title={'Enrolled Users'}>
                    {row.enroled_user}
                </TableCell>
            )
        },
        {
            name: __('Course Duration', 'moowoodle'),
            cell: (row) => (
                <TableCell title={'Date'}>
                    {row.date}
                </TableCell>
            )
        },
        {
            name: <div className="table-action-column">{__('Action', 'moowoodle')}{!appLocalizer.pro_active && <span className="admin-pro-tag">pro</span>}</div>,
            cell: (row, rowIndex) => (
                <div className="table-row-custom">
                    <h4 className="column-name">{__('Action', 'moowoodle')}{!appLocalizer.pro_active && <span className="admin-pro-tag">pro</span>}</h4>
                    <div class="moowoodle-course-actions">
                        <button
                            class={`sync-single-course button-primary`}
                            title={__('Sync Course Data')}
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
                            <option value="">{__('Bulk Actions')}</option>
                            <option value="sync_courses">{__('Sync Course')}</option>
                            <option value="create_product">{__('Create Product')}</option>
                            <option value="update_product">{__('Update Product')}</option>
                        </select>
                            {!appLocalizer.pro_active && <span className="admin-pro-tag">pro</span>}
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
        // {
        //     name: "courseField",
        //     render: (updateFilter, filterValue) => {
        //         return (
        //             <>
        //                 <div className="admin-header-search-section courseField">
        //                     <select
        //                         name="courseField"
        //                         onChange={(e) => updateFilter(e.target.name, e.target.value)}
        //                         value={filterValue || ""}
        //                     >
        //                         <option value="">Courses</option>
        //                         {Object.entries(courses).map(([courseId, courseName]) => (
        //                             <option value={courseId}>{courseName}</option>
        //                         ))}
        //                     </select>
        //                 </div>
        //             </>
        //         );
        //     },
        // },
        // {
        //     name: "productField",
        //     render: (updateFilter, filterValue) => {
        //         return (
        //             <>
        //                 <div className="admin-header-search-section productField">
        //                     <select
        //                         name="productField"
        //                         onChange={(e) => updateFilter(e.target.name, e.target.value)}
        //                         value={filterValue || ""}
        //                     >
        //                         <option value="">Products</option>
        //                         {Object.entries(products).map(([productId, productName]) => (
        //                             <option value={productId}>{productName}</option>
        //                         ))}
        //                     </select>
        //                 </div>
        //             </>
        //         );
        //     },
        // },
        {
            name: "catagoryField",
            render: (updateFilter, filterValue) => {
                return (
                    <>
                        <div className="admin-header-search-section catagoryField">
                            <select
                                name="catagoryField"
                                onChange={(e) => updateFilter(e.target.name, e.target.value)}
                                value={filterValue || ""}
                            >
                                <option value="">Category</option>
                                {Object.entries(category).map(([categoryId, categoryName]) => (
                                    <option value={categoryId}>{categoryName}</option>
                                ))}
                            </select>
                        </div>
                    </>
                );
            },
        },
        {
            name: "blank",
            render : () => {
                return(
                    <>
                    <div className="blank-separator"></div>
                    </>
                )
            }
        },
        {
            name: "shortnameField",
            render: (updateFilter, filterValue) => {
                return (
                    <>
                        <div className="admin-header-search-section shortnameField">
                            <select
                                name="shortnameField"
                                onChange={(e) => updateFilter(e.target.name, e.target.value)}
                                value={filterValue || ""}
                            >
                                <option value="">Short Name</option>
                                {Object.entries(shortName).map(([shortNameId, shortNameValue]) => (
                                    <option value={shortNameValue}>{shortNameValue}</option>
                                ))}
                            </select>
                        </div>
                    </>
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
                    placeholder={__("Search Course","moowoodle")}
                    onChange={(e) => updateFilter(e.target.name, e.target.value)}
                    value={filterValue || ""}
                  />
                </div>
              </>
            ),
        }
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
                        className="admin-font font-cross stock-manager-popup-cross"
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
                        <p>{__("All Courses", "moowoodle")}</p>
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
                            />
                        }
                    </div>
                </div>
            )
    );
}