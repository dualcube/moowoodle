import React, { useState, useEffect } from "react";
import axios from "axios";
import { __ } from "@wordpress/i18n";
import CustomTable, {
    TableCell,
} from "../AdminLibrary/CustomTable/CustomTable";


export default function Course() {
    const [data, setData] = useState(null);
    const [totalRows, setTotalRows] = useState();

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
            url: fetchSubscribersDataUrl,
            headers: { "X-WP-Nonce": appLocalizer.nonce },
            data: {
                page: currentPage,
                row: rowsPerPage,
            },
        }).then((response) => {
            const data = JSON.parse(response.data);

            setData(data);
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
        );
    };

    // Get the initial data for render
    useEffect(() => {
        requestData();
    }, []);

    // Get the total no of data present in database
    useEffect(() => {
        axios({
            method: "post",
            url: fetchSubscribersCount,
            headers: { "X-WP-Nonce": appLocalizer.nonce },
        }).then((response) => {
            response = response.data;
            console.log(response);
        });
    }, []);

    //columns for the data table
    const columns = [
        {
            name: __("Image", "woocommerce-stock-manager"),
            cell: (row) => <TableCell title="Image" >
                <img src={row.image} alt="product_image" />
            </TableCell>,
        },
        {
            name: __("Product", "woocommerce-stock-manager"),
            cell: (row) => <TableCell title="Product" > {row.product} </TableCell>,
        },
        {
            name: __("Email", "woocommerce-stock-manager"),
            cell: (row) =>
                <TableCell title="Email">
                    {row.email}
                    {
                        row.user_link &&
                        <a className="user-profile" href={row.user_link} target="_blank"><i className="admin-font font-person"></i></a>
                    }
                </TableCell>,
        },
        {
            name: __("Date", "woocommerce-stock-manager"),
            cell: (row) => <TableCell title="Date" > {row.date} </TableCell>,
        },
        {
            name: __("Status", "woocommerce-stock-manager"),
            cell: (row) => <TableCell title="status" >
                <p
                    className={row.status_key === 'mailsent' ? 'sent' : (row.status_key === 'subscribed' ? 'subscribed' : 'unsubscribed')}
                >{row.status}</p>
            </TableCell>,
        },
    ];

    return (
        <div className="course-container-wrapper">
            <div className="page-title">
                <p>{__("All Course", "moowoodle")}</p>
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
                    />
                }
            </div>
        </div>
    );
}
