import React, { useState, useEffect, useRef } from "react";
import DataTable from "react-data-table-component";
import "./table.scss";

const PENALTY = 28;
const COOLDOWN = 1;

// Loading table component.
const LoadingTable = () => {
  // Array to represent 10 rows
  const rows = Array.from({ length: 10 }, (_, index) => index);
  return (
    <>
      <table className="load-table">
        <tbody>
          {/* Loop to render 10 table rows */}
          {rows.map((row, rowIndex) => (
            <tr key={rowIndex}>
              {/* Loop to render 8 cells in each row */}
              {Array.from({ length: 5 }, (_, cellIndex) => (
                <td key={cellIndex} className="load-table-td">
                  <div className="line" />
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </>
  );
};

export const TableCell = (props) => {
  return (
    <>
      <div title={props.value} className="order-status table-row-custom">
        <h4 className="hide-title">{props.title}</h4> 
        {props.children}
      </div>
    </>
  );
};

const CustomTable = (props) => {
  const {
    data, // dataset for render table
    columns, // table column
    selectable, // option for select row column
    handleSelect, // callback function handle row select
    handlePagination, // callback function for handle pagination
    defaultRowsParPage, // default rows per page by user. if not set default is 10
    defaultCurrentPage, // default current page by user. if not set default is 1
    defaultTotalRows, // default total rows for the dataset. user should always provide this.
    perPageOption, // per page option array. user should always provide.
    realtimeFilter, // filter filds for realtime filter.
    autoLoading, // Filter variable for auto refresh on filter change.
    typeCounts,
    bulkActionComp,
  } = props;

  const [loading, setLoading] = useState(false); // loading state varaible.
  const [totalRows, setTotalRows] = useState(defaultTotalRows); // total no of row in dataset.
  const [rowsPerPage, setRowsPerPage] = useState(defaultRowsParPage || 10); // rows par page. default is 10.
  const [currentPage, setCurrentPage] = useState(defaultCurrentPage || 1); // current page state variable.
  // Realtime filter state variable
  const [filterData, setFilterData] = useState({});
  // Counter variable for cooldown effect
  const counter = useRef(0);
  const counterId = useRef(0);

  // Get the last fild of column.
  const sortableFild = columns[columns.length - 1];

  // Chek the last column field is dropdown
  // If not dropdown then push the dropdown field to the column.
  if (!sortableFild.isDropDown) {
    columns.push({
      name: "",
      cell: (row) => (
        <div className="table-dropdown_btn">
          <button onClick={(e) => handleTableExpand(e.currentTarget)}>
            <i class="admin-font adminLib-arrow-right"></i>
          </button>
        </div>
      ),
      isDropDown: true,
    });
  }

  // Function that handle table expand.
  const handleTableExpand = (e) => {
    e.children[0].classList.toggle('adminLib-arrow-down');
    e.children[0].classList.toggle('adminLib-arrow-right');
    const row = e.parentElement.parentElement.parentElement;
    row.classList.toggle("active");
  }

  // When new data comes, set loading to false.
  useEffect(() => {
    setTotalRows(defaultTotalRows);
    if (data === null) {
      setLoading(true);
    } else {
      setLoading(false);
    }
  }, [data, defaultTotalRows]);

  // Code for handle cooldown effect.
  useEffect(() => {
    // Check if filter data is empty then this effect is for first time rendering.
    // Do nothing in this case.
    if (Object.keys(filterData).length === 0) {
      return;
    }
    // Set counter by penalti
    counter.current = PENALTY;
    // Clear previous counter.
    if (counterId.current) {
      clearInterval(counterId.current);
    }
    // Create new interval
    const intervalId = setInterval(() => {
      counter.current -= COOLDOWN;
      // Cooldown compleate time for db request.
      if (counter.current < 0) {
        // Set the loading
        if (autoLoading) {
          setLoading(true);
        }
        // Call filter function
        handlePagination?.(rowsPerPage, 1, filterData);
        // Set current page to one.
        setCurrentPage(1);
        // Clear the interval.
        clearInterval(intervalId);
        counterId.current = 0;
      }
    }, 50);
    // Store the interval id.
    counterId.current = intervalId;
  }, [filterData]);

  // Handle mouse enter function.
  const handleMouseEnter = () => {
    props.handleMouseEnter?.();
  };

  // Handle mouse leave function.
  const handleMouseLeave = () => {
    props.handleMouseLeave?.();
  };

  const handlePageChange = async (newCurrentPage) => {
    // Start the loading...
    setLoading(true);
    // Call the function for handle pagination.
    handlePagination?.(rowsPerPage, newCurrentPage, filterData);
    // Set state variable
    setCurrentPage(newCurrentPage);
  };

  // Function handle rows-per-page change.
  const handleRowsPerPageChange = async (newRowsPerPage) => {
    // Start the loading...
    setLoading(true);
    // Call the function for handle pagination.
    handlePagination?.(newRowsPerPage, currentPage, filterData);
    // Set state variable.
    setCurrentPage(1);
    setRowsPerPage(newRowsPerPage);
  };

  // Function handle selected row change.
  const handleOnSelectedRowsChange = async ({
    selectedRows,
    selectedCount,
    allSelected,
  }) => {
    handleSelect?.(selectedRows, selectedCount, allSelected);
  };

  // Function that handle filter change.
  const handleFilterChange = (key, value) => {
    // Set filter data
    setFilterData((prevData) => {
      return {
        ...prevData,
        [key]: value,
      };
    });
  };

  // Contain which type count is currently active.
  const typeCountActive = filterData.typeCount || 'all';

  return (
    <div className={`table-container ${loading ? "table-loading" : ""} ${selectable ? "selectable-table" : ""}`}>
      <div className="admin-table-wrapper-filter">
        {typeCounts &&
          typeCounts.map((countInfo, index) => (
            <div
              key={index} // Add a key for better React performance
              onClick={(e) => {
                setFilterData({ typeCount: countInfo.key });
              }}
              className={countInfo.key === typeCountActive ? 'type-count-active' : ''}
            >
              {`${countInfo.name} (${countInfo.count})`} 
              {index !== typeCounts.length - 1 && ' |'} {/* Add '|' except for the last item */}
            </div>
          ))}
      </div>

      <div className="filter-wrapper">
        <div className="wrap-bulk-all-date">
          {/* Render realtime filter */}
          {realtimeFilter &&
            realtimeFilter.map((filter) => {
              return filter.render(handleFilterChange, filterData[filter.name]);
            })}
        </div>
        {bulkActionComp && bulkActionComp()}
      </div>
      {loading ? (
        <LoadingTable />
      ) : (
        <DataTable
          pagination
          paginationServer
          selectableRows={selectable}
          columns={columns}
          data={data || []}
          // Pagination details.
          paginationTotalRows={totalRows}
          paginationDefaultPage={currentPage}
          paginationPerPage={rowsPerPage}
          paginationRowsPerPageOptions={perPageOption}
          // Mouse enter leave callback.
          onRowMouseEnter={handleMouseEnter}
          onRowMouseLeave={handleMouseLeave}
          // Pagination callback.
          onChangePage={handlePageChange}
          onChangeRowsPerPage={handleRowsPerPageChange}
          // Row select callback.
          onSelectedRowsChange={handleOnSelectedRowsChange}
        />
      )}
    </div>
  );
};

export default CustomTable;