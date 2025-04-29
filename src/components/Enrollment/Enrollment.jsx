import React, { useState, useEffect, useRef } from 'react';
import { __ } from "@wordpress/i18n";
import CustomTable, { TableCell } from "../AdminLibrary/CustomTable/CustomTable";
import { getApiLink } from "../../services/apiService";
import axios from 'axios';
import Dialog from "@mui/material/Dialog";
import { DateRangePicker } from 'react-date-range';
import 'react-date-range/dist/styles.css'; // main style file
import 'react-date-range/dist/theme/default.css'; // theme css file
import './Enrollment.scss';
import Popoup from "../PopupContent/PopupContent.jsx";
import defaultImage from '../../assets/images/moowoodle-product-default.png';

const Enrollment = () => {
	const [courses, setCourses] = useState([]);
	const [groups, setGroups] = useState([]);
	const [cohorts, setCohorts] = useState([]);
	const [data, setData] = useState(null);
	const [selectedRows, setSelectedRows] = useState([]);
	const [enrollemntStatus, setEnrollemntStatus] = useState(null);
	const [totalRows, setTotalRows] = useState();
	const [openDialog, setOpenDialog] = useState(false);
	const [openDatePicker, setOpenDatePicker] = useState(false);
	const dateRef = useRef();

	useEffect(() => {
		document.body.addEventListener("click", (event) => {
			if (!dateRef?.current?.contains(event.target)) {
				setOpenDatePicker(false);
			}
		})
	}, [])

	useEffect(() => {
		if (appLocalizer.khali_dabba) {
			axios({
				method: "post",
				url: getApiLink('get-enrollments'),
				headers: { "X-WP-Nonce": appLocalizer.nonce },
				data: {
					counts: true
				},
			}).then((response) => {
				setTotalRows(response.data);
			});
		}
	}, []);

	useEffect(() => {
		if (appLocalizer.khali_dabba) {
			requestData();
		}
	}, []);

	useEffect(() => {
		axios({
			method: "get",
			url: getApiLink('all-courses'),
			headers: { "X-WP-Nonce": appLocalizer.nonce },
		}).then((response) => {
			setCourses(response.data.courses)
		});
	}, []);
	useEffect(() => {
		axios({
			method: "get",
			url: getApiLink('all-groups'),
			headers: { "X-WP-Nonce": appLocalizer.nonce },
		}).then((response) => {
			setGroups(response.data.groups)
			setCohorts(response.data.cohorts)
		});
	}, []);

	useEffect(() => {
		if (appLocalizer.khali_dabba) {
			axios({
				method: "post",
				url: getApiLink('get-enrollments'),
				headers: { "X-WP-Nonce": appLocalizer.nonce },
				data: { segment: true },
			}).then((response) => {
				response = response.data;

				setEnrollemntStatus([
					{
						key: "all",
						name: __("All", "moowoodle"),
						count: response["all"],
					},
					{
						key: "enrolled",
						name: __("Enrolled", "moowoodle"),
						count: response["enrolled"],
					},
					{
						key: "unenrolled",
						name: __("Unenrolled", "moowoodle"),
						count: response["unenrolled"],
					},
				]);
			});
		}
	}, []);

	const handleDateOpen = () => {
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
		search_student_field = "",
		search_student_action = "",
		courseField = "",
		groupField = "",
		cohortField = "",
		typeCount = "",
		start_date = new Date(0),
		end_date = new Date()
	) {

		//Fetch the data to show in the table
		axios({
			method: "post",
			url: getApiLink('get-enrollments'),
			headers: { "X-WP-Nonce": appLocalizer.nonce },
			data: {
				page: currentPage,
				row: rowsPerPage,
				student: search_student_field,
				student_action: search_student_action,
				course: courseField,
				group: groupField,
				cohort: cohortField,
				status: typeCount == 'all' ? '' : typeCount,
				start_date: start_date,
				end_date: end_date,
			},
		}).then((response) => {
			setData(response.data);
		});
	}

	const requestApiForData = (rowsPerPage, currentPage, filterData = {}) => {

		// If serch action or search text fields any one of is missing then do nothing 
        if ( Boolean( filterData?.search_student_field ) ^ Boolean( filterData?.search_student_action ) ) {
            return;
		}
		
		setData(null);

		requestData(
			rowsPerPage,
			currentPage,
			filterData?.search_student_field,
			filterData?.search_student_action,
			filterData?.courseField,
			filterData?.groupField,
			filterData?.cohortField,
			filterData?.typeCount,
			filterData?.date?.start_date,
			filterData?.date?.end_date,
		);
	};

	const realtimeFilter = [
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
			name: "groupField",
			render: (updateFilter, filterValue) => {
				return (
					<>
						<div className="admin-header-search-section">
							<select
								name="groupField"
								onChange={(e) => updateFilter(e.target.name, e.target.value)}
								value={filterValue || ""}
							>
								<option value="">Groups</option>
								{Object.entries(groups).map(([groupId, groupName]) => (
									<option value={groupId}>{groupName}</option>
								))}
							</select>
						</div>
					</>
				);
			},
		},
		{
			name: "cohortField",
			render: (updateFilter, filterValue) => {
				return (
					<>
						<div className="admin-header-search-section">
							<select
								name="cohortField"
								onChange={(e) => updateFilter(e.target.name, e.target.value)}
								value={filterValue || ""}
							>
								<option value="">Cohorts</option>
								{Object.entries(cohorts).map(([cohortId, cohortName]) => (
									<option value={cohortId}>{cohortName}</option>
								))}
							</select>
						</div>
					</>
				);
			},
		},
		{
			name: "date",
			render: (updateFilter, value) => (
				<div ref={dateRef}>
					<div className="admin-header-search-section">
						<input
							value={
								selectedRange[0].startDate
									? `${selectedRange[0].startDate.toLocaleDateString()} - ${selectedRange[0].endDate.toLocaleDateString()}`
									: '-- -- ----' // Placeholder value when startDate is null
							}
							onClick={() => handleDateOpen()}
							className="date-picker-input-custom"
							type="text"
							placeholder={__("DD/MM/YYYY", "moowoodle")}
						/>
					</div>
					{openDatePicker &&
						<div className="date-picker-section-wrapper">
							<DateRangePicker
								ranges={selectedRange}
								months={1}
								direction="vertical"
								scroll={{ enabled: true }}
								maxDate={new Date()}
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
		{
			name: "search_student_field",
			render: (updateFilter, filterValue) => (
				<>
					<div className="admin-header-search-section search_student_field">
						<input
							name="search_student_field"
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
			name: "search_student_action",
			render: (updateFilter, filterValue) => {
				return (
					<>
						<div className="admin-header-search-section search_student_action">
							<select
								name="search_student_action"
								onChange={(e) => updateFilter(e.target.name, e.target.value)}
								value={filterValue || ""}
							>
								<option value="" >Select</option>
								<option value="name" >Name</option>
								<option value="email">Email</option>
							</select>
						</div>
					</>
				);
			},
		}
	];

	//columns for the data table
	const columns = [
		{
			name: __("Course", "moowoodle"),
			cell: (row) =>
				<TableCell title="course_name" >
					<img src={row.course_img || defaultImage} alt="" />
					<div className="action-section">
						<p>{row.course_name || row.group_name || row.cohort_name}</p>
						{/* <div className='action-btn'>
							<a target='_blank' href={row.course_url} className="">Edit link product</a>
						</div> */}
					</div>
				</TableCell>,
		},
		{
			name: __("Student", "moowoodle"),
			cell: (row) =>
				<TableCell title="student_name">
					{
						row.customer_img ?
							(
								<span dangerouslySetInnerHTML={{ __html: row.customer_img }}></span>
							): (
								<img src={defaultImage} alt="defaultimage" />
							)
					}
					
					<div className="action-section">
						<p>{row.customer_name}</p>
						<div className='action-btn'>
							<a target='_blank' href={row.customer_url} className="">Edit user</a>
						</div>
					</div>
				</TableCell>,
		},
		{
			name: __("Enrollment Date", "moowoodle"),
			cell: (row) => <TableCell title="Date" > {row.date} </TableCell>,
			sortable: true,
		},
		{
			name: __("Status", "moowoodle"),
			cell: (row) => (
				<TableCell title="Status">
					<div className='action-section'>
						<button className={`status-show-btn ${row.status === 'enrolled' ? 'enroll' : 'unenroll'}`}	>
							{row.status === 'enrolled' ? 'Enrolled' : 'Unenrolled'}
						</button>
						<div className='action-btn'>
							<button className={row.status === 'enrolled' ? 'unenroll' : 'enroll'} onClick={() => handleButtonClick(row)}>{row.status === 'enrolled' ? 'Unenroll Now' : 'Enroll Now'}</button>
						</div>
					</div>
				</TableCell>
			),
		}

	];

	const handleButtonClick = (row) => {

		if (confirm('Are you sure you want to proceed?') === true) {
			axios({
				method: 'post',
				url: getApiLink('manage-enrollment'),
				headers: { "X-WP-Nonce": appLocalizer.nonce },
				data: {
					order_id: row.order_id,
					course_id: row.course_id,
					user_id: row.customer_id,
					action: row.status == 'enrolled' ? 'unenroll' : 'enrolled'
				},
			}).then((response) => {
				requestData();
			});
		}
	}

	return (
		<>
			{!appLocalizer.khali_dabba ? (
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
							className="admin-font adminLib-cross stock-manager-popup-cross"
							onClick={() => {
								setOpenDialog(false);
							}}
						></span>
						<Popoup />
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
							selectable={true}
							handleSelect={(selectRows) => {
								setSelectedRows(selectRows);
							}}
							handlePagination={requestApiForData}
							defaultRowsParPage={10}
							defaultTotalRows={totalRows}
							perPageOption={[10, 25, 50]}
							realtimeFilter={realtimeFilter}
							typeCounts={enrollemntStatus}
							autoLoading={false}
						/>
					}
				</div>
			)}
		</>

	);
}
export default Enrollment;