import React, { useState, useEffect, useRef } from 'react';
import { __ } from "@wordpress/i18n";
import CustomTable, {TableCell} from "../AdminLibrary/CustomTable/CustomTable";
import { getApiLink } from "../../services/apiService";
import axios from 'axios';
import Dialog from "@mui/material/Dialog";
import { DateRangePicker } from 'react-date-range';
import 'react-date-range/dist/styles.css'; // main style file
import 'react-date-range/dist/theme/default.css'; // theme css file
import './Enrollment.scss';
import Propopup from "../PopupContent/PopupContent";

const Enrollment = () => {
	const [students, setStudents] = useState([]);
    const [postStatus, setPostStatus] = useState("");
	const [suggestions, setSuggestions] = useState([]);
	const [courses, setCourses] = useState([]);
    const [data, setData] = useState(null);
    const [selectedRows, setSelectedRows] = useState([]);
    const [totalRows, setTotalRows] = useState();
    const [openDialog, setOpenDialog] = useState(false);
    const [openDatePicker, setOpenDatePicker] = useState(false);
    const [openModal, setOpenModal] = useState(false);
    const [modalDetails, setModalDetails] = useState(false);
	const dateRef = useRef();

	useEffect(() => {
		document.body.addEventListener("click", (event) => {
			if (! dateRef?.current?.contains(event.target) ) {
			  setOpenDatePicker(false);
			}
		})
	}, [])

	useEffect(() => {
		axios({
			method: "post",
			url:  getApiLink('get-enrollments'),
			headers: { "X-WP-Nonce": appLocalizer.nonce },
			data: {
				counts : true
			},
		  }).then((response) => {
			setTotalRows(response.data);
		  });
	}, []);

	useEffect(() => {
		if (appLocalizer.pro_active) {
		  requestData();
		}
	}, []);

	
	useEffect(() => {
		axios({
			method: "get",
			url: getApiLink('all-customers'),
		}).then((response) => {
			setStudents(response.data)
		});
	}, []);

	useEffect(() => {
		axios({
			method: "get",
			url: getApiLink('all-courses'),
		}).then((response) => {
			setCourses(response.data)
		});
	}, []);

    const handleDateOpen = ()=>{
        setOpenDatePicker(!openDatePicker);
      }
    
    const [selectedRange, setSelectedRange] = useState([
    {
        startDate: new Date(),
        endDate: new Date(),
        key: 'selection'
    }
    ]);

	function requestData(
		rowsPerPage = 10,
		currentPage = 1,
		studentField = "",
		courseField = "",
		statusField = "",
		start_date = new Date(0),
		end_date = new Date(),
		postStatus
	  ) {

		//Fetch the data to show in the table
		axios({
		  method: "post",
		  url:  getApiLink('get-enrollments'),
		  headers: { "X-WP-Nonce": appLocalizer.nonce },
		  data: {
			page: currentPage,
			row: rowsPerPage,
			student: studentField,
			course: courseField,
			status: statusField,
			start_date: start_date,
			end_date: end_date,
		  },
		}).then((response) => {
		  	setData(response.data);
		});
	  }
	
	const requestApiForData = (rowsPerPage, currentPage, filterData = {}) => {
		// console.log(filterData)
		requestData(
		  rowsPerPage,
		  currentPage,
		  filterData?.studentField,
		  filterData?.courseField,
		  filterData?.statusField,
		  filterData?.date?.start_date,
		  filterData?.date?.end_date,
		);
	};

	const realtimeFilter = [
		{
			name: "studentField",
			render: (updateFilter, filterValue) => {
			return (
			<>
				<div className="admin-header-search-section">
				<select
					name="studentField"
					onChange={(e) => updateFilter(e.target.name, e.target.value)}
					value={filterValue || ""}
				>
					<option value="">Student</option>
					{Object.entries(students).map(([userId, userName]) => (
						<option value={userId}>{userName}</option>
					))}
				</select>
				</div>
			</>
			);
			},
		},
		
		{
			name: "courseField",
			render: (updateFilter, filterValue) => {
			return (
			<>
				<div className="admin-header-search-section">
				<select
					name="courseField"
					onChange={(e) => updateFilter(e.target.name, e.target.value)}
					value={filterValue || ""}
				>
					<option value="">Courses</option>
					{Object.entries(courses).map(([courseId, courseName]) => (
						<option value={courseId}>{courseName}</option>
					))}
				</select>
				</div>
			</>
			);
			},
		},

		{
		  name: "statusField",
		  render: (updateFilter, filterValue) => (
			<>
			  <div className="admin-header-search-section">
			  <select 
					name="statusField"
					placeholder={__("All Statuses", "moowoodle")}
					onChange={(e) => updateFilter(e.target.name, e.target.value)}
					value={filterValue || ""}
					>
					<option value="">All statuses</option>
					<option value="enrolled">Enroll</option>
					<option value="unenrolled">Unenroll</option>
            	</select>
			  </div>
			</>
		  ),
		},
		{
		  name: "date",
		  render: (updateFilter, value) => (
			<div ref={dateRef}>
			  <div className="admin-header-search-section">
				<input value={`${selectedRange[0].startDate.toLocaleDateString()} - ${selectedRange[0].endDate.toLocaleDateString()}`} onClick={()=>handleDateOpen()} className="date-picker-input-custom" type="text" placeholder={__("DD/MM/YYYY", "moowoodle")} />
			  </div>
			  {openDatePicker &&
			   <div className="date-picker-section-wrapper">
				<DateRangePicker
				  ranges={selectedRange}
				  months={1}
				  direction="vertical"
				  scroll={{ enabled: true }}
				  maxDate={ new Date() }
				  shouldDisableDate={date => isAfter(date, new Date())}
				  onChange={(dates) => {
					if (dates.selection) {
					  dates = dates.selection;
					  dates.endDate?.setHours(23, 59, 59, 999)
					  setSelectedRange([dates])
					  updateFilter("date", {
						start_date: dates.startDate,
						end_date: dates.endDate,
					  });
					}
				  }}
				/>
				</div>
			  }
			</div>
		  ),
		},
	  ];
	
	//columns for the data table
	const columns = [
	{
		name: __("Course", "moowoodle"),
		cell: (row) => 
		<TableCell title="course_name" >
			<p>{ row.course_name }</p> 
		</TableCell>,
	},
	{
		name: __("Student", "moowoodle"),
		cell: (row) =>
		<TableCell title="student_name">
			<p>{row.customer_name}</p>
		</TableCell>,
	},
	{
		name: __("Enrollment Date", "moowoodle"),
		cell: (row) => <TableCell title="Date" > {row.date} </TableCell>,
	},
	{
		name: __("Status", "moowoodle"),
		cell: (row) => <TableCell title="Status" > 
		<p>{row.status == 'enrolled' ? 'Enrolled' : 'Unenrolled'}</p>
		</TableCell>,
	},
	{
		name: __("Action", "moowoodle"),
		cell: (row) => (
		  <TableCell title="Action">
			<button className={`${row.status == 'enrolled' ? 'unenroll' : 'enroll'}`} onClick={() => handleButtonClick(row)}>
			  {row.status === 'enrolled' ? 'Unenroll' : 'Enroll'}
			</button>
		  </TableCell>
		),
	  }	  
	  
	];

	const handleButtonClick = (row) => {
		console.log(row)
		
		if ( confirm('Are you sure you want to proceed?') === true ) {
			axios({
				method: 'post',
				url: getApiLink('manage-enrollment'),
				data: {
					orderId : row.order_id,
					courseId : row.course_id,
					userId : row.customer_id,
					action : row.status
				},
			}).then((response) => {
				requestData();
			});
		}
	}
      
    return(
        <>
		{ ! appLocalizer.pro_active ? (
			<>
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
          <div
            className="enrollment-img"
            onClick={() => {
              setOpenDialog(true);
            }}>
          </div>
		</>
      ) : (
        <div className="admin-enrollment-list">
          <div className="admin-page-title">
            <p>{__("All Enrollments", "moowoodle")}</p>
          </div>
            {
                <CustomTable
                data={data}
                columns={columns}
                selectable = {true}
                handleSelect = {(selectRows) => {
                  setSelectedRows(selectRows);
                }}
                handlePagination={requestApiForData}
                defaultRowsParPage={10}
                defaultTotalRows={totalRows}
                perPageOption={[10, 25, 50]}
                realtimeFilter={realtimeFilter}
              />
            }
          </div>
		  )}
        </>
        
    );
}
export default Enrollment;
