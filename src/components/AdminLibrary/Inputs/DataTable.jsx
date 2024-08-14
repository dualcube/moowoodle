import React from 'react'
import ReactPaginate from "react-paginate";
import DataTable from 'react-data-table-component';

//props list
// setCurrentPage, setRowsPerPage(this should be the set state variable functions passed as props)
// rowsPerPageText, rowsPerPage, rowsPerPageOptions, wrapperClass, dataTableClass, data, columns, selectableRows, 

export default function DataTable({props}) {
    const override = css`
        display: block;
        margin: 0 auto;
        border-color: red;
    `;


    const Pagination = () => {
        const handlePageChange = ( { selected } ) => {
            props.setCurrentPage ( selected );
            window.scrollTo ( {
            top: 0,
            behavior: 'smooth',
            } );
        };
        const handleRowsPerPageChange = ( e ) => {
            props.setRowsPerPage( parseInt ( e.target.value ) );
            window.scrollTo({
            top: 0,
            behavior: 'smooth',
            });
            props.setCurrentPage( 0 );
        };
        return(
            <div className="pagination">
                <div>
                    <label htmlFor={ props.rowsPerPageText } > { props.rowsPerPageText } </label>
                    <select id={ props.rowsPerPageText } value={ props.rowsPerPage } onChange={ handleRowsPerPageChange } >
                        {
                            props.rowsPerPageOptions.map( ( options ) => {
                            return <option value={ options }> { options } </option>
                            })
                        }
                        <option value={ props.totalRows }>{"All"}</option>
                    </select>          
                </div>
                <ReactPaginate
                    className="pagination"
                    previousLabel={"previous"}
                    nextLabel={"next"}
                    breakLabel={"..."}
                    breakClassName={"break-me"}
                    pageCount={ props.totalRows ? Math.ceil ( props.totalRows / props.rowsPerPage ) : 0 }
                    marginPagesDisplayed={2}
                    pageRangeDisplayed={2}
                    onPageChange={ handlePageChange }
                />
            </div>
        )
    }
  return(
            <div className={ props.wrapperClass }>
                <DataTable
                    className={ props.dataTableClass }
                    columns={ props.columns  }
                    data={ props.data }
                    selectableRows={ props.selectableRows }
                />
                { Pagination() }
            </div>
        )
  
}
