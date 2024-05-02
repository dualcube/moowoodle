import React, { useState, useEffect } from "react";
import axios from "axios";
import { getApiLink } from "../../services/apiService";
import CustomTable, {
    TableCell,
} from "../AdminLibrary/CustomTable/CustomTable";
import Banner from "../Banner/banner";
import { useRef } from "react";
import './courses.scss';

export default function Course() {
    const { __ } = wp.i18n;
    const [data, setData] = useState(null);
    const [selectedRows, setSelectedRows] = useState([]);
    const [totalRows, setTotalRows] = useState();
    const bulkSelectRef = useRef();
    
    /**
     * Function that request data from backend
     * @param {*} rowsPerPage 
     * @param {*} currentPage 
     */
    function requestData(
        rowsPerPage = 10,
        currentPage = 1,
    ) {
        //Fetch the data to show in the table
        axios({
            method: "post",
            url: getApiLink('courses'),
            headers: { "X-WP-Nonce": appLocalizer.nonce },
            data: {
                page: currentPage,
                perpage: rowsPerPage,
            },
        }).then((response) => {
            // const data = JSON.parse(response.data);
            setData(response.data);
        });
    }

    /**
     * Callback function for request data from rest api.
     * @param {*} rowsPerPage 
     * @param {*} currentPage 
     * @param {*} filterData 
     */
    const requestApiForData = ( rowsPerPage, currentPage, filterData = {} ) => {
        requestData(
            rowsPerPage,
            currentPage,
        );
    };

    /**
     * Handle single row action
     * @param {*} actionName 
     * @param {*} courseId 
     * @param {*} rowId 
     * @param {*} rowIndex 
     */ 
    const handleSingleAction = ( actionName, courseId, moodleCourseId ) => {
        if ( appLocalizer.pro_active ) {
            axios({
                method: 'post',
                url: getApiLink( `course-bulk-action` ),
                headers: { 'X-WP-Nonce' : appLocalizer.nonce },
                data: {
                    selected_action: actionName,
                    course_ids: [{
                        course_id: courseId,
                        moodle_course_id: moodleCourseId,
                    }]
                },
            }).then( ( response ) => {
                console.log(response);
            }).catch((error) => {
                console.error('Error:', error);
            });
        } else {
            console.log("pro banner");
        }
    }

    const handleBulkAction = (event) => {
        if ( appLocalizer.pro_active ) {
            if ( ! selectedRows.length ) {
                return window.alert( __( 'Select rows', 'moowoodle' ) );
            }
            if ( ! bulkSelectRef.current.value ) {
                return window.alert( __( 'Select bulk action' ,'moowoodle') );
            }

            axios({
                method: 'post',
                url: getApiLink( `course-bulk-action` ),
                headers: { 'X-WP-Nonce' : appLocalizer.nonce },
                data: {
                    selected_action: bulkSelectRef.current.value,
                    course_ids: selectedRows.map((row) => {
                        return { course_id: row.id, moodle_course_id: row.moodle_course_id }
                    })
                },
            }).then( ( response ) => {
                console.log(response);
            }).catch((error) => {
                console.error('Error:', error);
            });
        } else {
            console.log("pro banner");
        }
    }

    const handleRowSelect = (selectedRows, selectedCount, allSelect) => {
        setSelectedRows(selectedRows);
    }

    // Get the total no of data present in database
    useEffect(() => {
        axios({
            method: "post",
            url: getApiLink('courses'),
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
                    <a href={ row.moodle_url } alt="moowoodle_url">
                        { row.course_name }
                    </a>
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
                    <a href={ row.category_url } alt="category_url">
                        { row.category_name }
                    </a>
                </TableCell>
            ),
            sortable: true,
        },
        {
            name: __('Enrolled Users', 'moowoodle'),
            cell: (row) => (
                <TableCell title={'Enrolled Users'}>
                    { row.enroled_user }
                </TableCell>
            )
        },
        {
            name: __('Date', 'moowoodle'),
            cell: (row) => (
                <TableCell title={'Date'}>
                    { row.date }
                </TableCell>
            )
        },
        {
            name: <div dangerouslySetInnerHTML={{ __html: __('Actions')}}></div>,
            cell: ( row, rowIndex ) => (
                <div class="moowoodle-course-actions">
                    <button
                        class={ `sync-single-course button-primary` }
                        title={ __('Sync Couse Data') }
                        onClick={ (e) => {
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
                        Object.keys( row.products ).length ?
                        <button
                            class={ `update-existed-single-product button-secondary ` }
                            title={ __('Sync Course Data & Update Product') }
                            onClick={ (e) => {
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
                            class={ `create-single-product button-secondary` }
                            title={ __('Create Product') }
                            onClick={ (e) => {
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

    return (
        <div className="course-container-wrapper">
            <div className="admin-page-title">
                <p>{__("All Course", "moowoodle")}</p>
            </div>
            <div className="course-bulk-action">
                <label>
                    { __( 'Select bulk action' ) }
                </label>
                <select name="action" ref={bulkSelectRef} >
                    <option value="">{ __( 'Bulk Actions' ) }</option>
                    <option value="sync_courses">{ __( 'Sync Course' ) }</option>
                    <option value="create_product">{ __( 'Create Product' ) }</option>
                    <option value="update_product">{__('Update Product')}</option>
                </select>
                <button
                    name="bulk-action-apply"
                    onClick={handleBulkAction}
                >
                    {__('Apply',)}
                </button>
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
                    />
                }
            </div>
        </div>
    );
}
