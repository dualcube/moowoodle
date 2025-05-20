// import React, { useState, useEffect } from "react";
// import { __ } from "@wordpress/i18n";
// import axios from "axios";
// import Select from "react-select";
// import { getApiLink } from "../../services/apiService";
// import "./MyClassroom.scss";

// const ViewEnroll = ({ classroom }) => {
//   const [enrolledStudents, setEnrolledStudents] = useState([]);
//   const [availableCourses, setAvailableCourses] = useState([]);
//   const [showForm, setShowForm] = useState(false);
//   const [isLoading, setIsLoading] = useState(false);
//   const [newStudent, setNewStudent] = useState({
//     first_name: "",
//     last_name: "",
//     email: "",
//     courses: [],
//   });
//   const [currentPage, setCurrentPage] = useState(1);
//   const [totalPages, setTotalPages] = useState(1);
//   const [csvFile, setCsvFile] = useState(null);
//   const [showBulkModal, setShowBulkModal] = useState(false);
//   const [totalCourses, setTotalCourses] = useState(0);
//   const [totalEnrolled, setTotalEnrolled] = useState(0);
//   const [totalAvailable, setTotalAvailable] = useState(0);
//   const [showUnenrollModal, setShowUnenrollModal] = useState(false);
//   const [selectedStudentForUnenroll, setSelectedStudentForUnenroll] =
//     useState(null);
//   const [unenrollCourses, setUnenrollCourses] = useState([]);

//   const studentsPerPage = 5;

//   const defaultImageUrl =
//     "https://cus.dualcube.com/mvx2/wp-content/uploads/2025/04/beanie-2-3-416x416.jpg";

//   const fetchClassroomCourses = async () => {
//     try {
//       if (classroom?.type !== 'classroom') return;

//       const response = await axios.get(getApiLink("view-classroom"), {
//         params: {
//           type: classroom.type,
//           classroom_id: classroom.classroom_id,
//         },
//         headers: { "X-WP-Nonce": appLocalizer?.nonce },
//       });

//       const data = response.data;

//       if (data.success) {
//         const { courses = [], total_courses = 0 } = data;

//         const totalAvailable = courses.reduce(
//           (sum, course) => sum + Number(course.available_quantity || 0),
//           0
//         );

//         setAvailableCourses(courses);
//         setTotalCourses(total_courses);
//         setTotalAvailable(totalAvailable);
//       } else {
//         console.error("Failed to load classroom courses:", data.message);
//       }
//     } catch (error) {
//       console.error(__("Error fetching classroom courses:", "moowoodle"), error);
//     }
//   };

//   const fetchClassroomData = async (page = 1) => {
//     try {
//       let payload = {};

//       if (classroom?.type === 'classroom') {
//         payload = {
//           classroom_type: classroom?.type,
//           classroom_id: classroom?.classroom_id
//         };
//       }

//       if (classroom?.type === 'cohort' || classroom?.type === 'group') {
//         payload = {
//           classroom_type: classroom?.type,
//           group_item_id: classroom?.group_item_id
//         };
//       }

//       let response = await axios.get(getApiLink("enrollments"), {
//         params: {
//           ...payload,
//           page,
//           per_page: studentsPerPage,
//         },
//         headers: { "X-WP-Nonce": appLocalizer?.nonce },
//       });


//       if (response.data.success) {
//         setEnrolledStudents(response.data.data.students || []);
//         setTotalEnrolled(response.data.data.total_enrolled || 0);
//         setTotalAvailable(totalAvailable);
//         setTotalPages(response.data.pagination?.total_pages || 1);
//         setCurrentPage(response.data.pagination?.current_page || page);
//       } else {
//         console.error("Failed to load data:", response.message);
//       }

//     } catch (error) {
//       console.error(__("Error fetching classroom data:", "moowoodle"), error);
//     }
//   };

//   useEffect(() => {
//     fetchClassroomCourses();
//     fetchClassroomData(currentPage);
//   }, [currentPage]);

//   const courseOptions = availableCourses.map((item) => ({
//     value: item.course_id,
//     label: item.course_name,
//     item_id: item.item_id,
//     group_item_id: item.group_item_id,
//   }));

//   const handleInputChange = (e) => {
//     setNewStudent({ ...newStudent, [e.target.name]: e.target.value });
//   };

//   const handleCourseChange = (selectedOptions) => {
//     const courses =
//       selectedOptions?.map((option) => ({
//         course_id: option.value,
//         item_id : option.item_id,
//         group_item_id: option.group_item_id,
//         course_name: option.label,
//       })) || [];
//     setNewStudent({ ...newStudent, courses });
//   };

//   const handleEnrollStudent = async (e) => {
//     e.preventDefault();

//     const { first_name, last_name, email, courses } = newStudent;
//     const { type, classroom_id } = classroom;
//     // Basic validation
//     const isEmpty = !first_name || !last_name || !email;
//     const isClassroomCourseMissing =
//       type === "classroom" && (!Array.isArray(courses) || courses.length === 0);

//     if (isEmpty || isClassroomCourseMissing) {
//       alert(__("Please fill in all required fields.", "moowoodle"));
//       return;
//     }
//     setIsLoading(true);

//     // Build payload
//     const payload = {
//       type,
//       first_name,
//       last_name,
//       email,
//       ...(type === "classroom" && {
//         classroom_id,
//         order_id: classroom.order_id || 0,
//         course_selections: courses.map(({ course_id,item_id, group_item_id }) => ({
//           course_id,
//           item_id,
//           classroom_item_id: group_item_id,
//         })),
//       }),
//       ...(type === "cohort" && {
//         cohort_id: classroom.cohort_id,
//         order_id:classroom.order_id,
//         item_id:classroom.item_id,
//         cohort_item_id: classroom.group_item_id,
//       }),
//       ...(type === "group" && {
//         group_id: classroom.group_id,
//         order_id:classroom.order_id,
//         item_id:classroom.item_id,
//         group_item_id: classroom.group_item_id,
//       }),
//     };
//     // Fallback if type is unexpected
//     if (!["classroom", "cohort", "group"].includes(type)) {
//       alert(__("Invalid enrollment type.", "moowoodle"));
//       setIsLoading(false);
//       return;
//     }

//     try {
//       const response = await axios.post(getApiLink("enroll"), payload, {
//         headers: { "X-WP-Nonce": appLocalizer?.nonce },
//       });

//       if (response.data.success) {
//         setShowForm(false);
//         setNewStudent({ first_name: "", last_name: "", email: "", courses: [] });
//         await fetchClassroomData(1);
//         await fetchClassroomCourses();
//         alert(__("Enrollment successful! The classroom data has been updated.", "moowoodle"));
//       } else {
//         alert(
//           __("Enrollment failed: ", "moowoodle") +
//           (response.data.message || __("Unknown error", "moowoodle"))
//         );
//       }
//     } catch (error) {
//       console.error("Enrollment error:", error);
//       alert(__("Error enrolling student. Please try again.", "moowoodle"));
//     }

//     setIsLoading(false);
//   };


//   const handleCsvUpload = async (e) => {
//     e.preventDefault();
  
//     if (!csvFile) {
//       alert(__("Please select a CSV file.", "moowoodle"));
//       return;
//     }
  
//     const { type, classroom_id, order_id = 0, cohort_id, item_id, group_id, group_item_id } = classroom;
  
//     if (type === "classroom" && (!Array.isArray(availableCourses) || !availableCourses.length)) {
//       alert(__("No courses available for classroom enrollment.", "moowoodle"));
//       return;
//     }
  
//     if (!["classroom", "cohort", "group"].includes(type)) {
//       alert(__("Invalid enrollment type.", "moowoodle"));
//       return;
//     }
  
//     setIsLoading(true);
  
//     const payload = { type, order_id };
  
//     if (type === "classroom") {
//       payload.classroom_id = classroom_id;
//       payload.course_selections = availableCourses.map(course => ({
//         course_id: String(course.course_id),
//         item_id: String(course.item_id),
//         group_item_id: String(course.group_item_id || 0),
//       }));
//     } else if (type === "cohort") {
//       Object.assign(payload, {
//         cohort_id,
//         item_id,
//         cohort_item_id: group_item_id,
//       });
//     } else if (type === "group") {
//       Object.assign(payload, {
//         group_id,
//         item_id,
//         group_item_id,
//       });
//     }
  
//     const formData = new FormData();
//     formData.append("file", csvFile);
  
//     for (const [key, value] of Object.entries(payload)) {
//       formData.append(key, typeof value === "object" ? JSON.stringify(value) : value);
//     }
    
//     try {
//       const response = await axios.post(getApiLink("bulk-enroll"), formData, {
//         headers: {
//           "X-WP-Nonce": appLocalizer?.nonce,
//           "Content-Type": "multipart/form-data",
//         },
//       });
  
//       if (response.data.success) {
//         setCsvFile(null);
//         setShowBulkModal(false);
//         await fetchClassroomData(1); // refresh
//         await fetchClassroomCourses();
//         alert(__("Bulk enrollment successful! ", "moowoodle") + response.data.message);
//       } else {
//         alert(__("Bulk enrollment failed: ", "moowoodle") + (response.data.message || __("Unknown error", "moowoodle")));
//       }
//     } catch (error) {
//       console.error("Bulk enrollment error:", error);
//       alert(__("Error during bulk enrollment. Please try again.", "moowoodle"));
//     } finally {
//       setIsLoading(false);
//     }
//   };
  
  
//   const handleUnenrollStudent = async () => {

//     if (!unenrollCourses.length) {
//       alert(__("Please select at least one course to unenroll.", "moowoodle"));
//       return;
//     }

//     try {
//       setIsLoading(true);
//       const response = await axios.post(
//         getApiLink("unenroll"),
//         {
//           type: 'course',
//           user_id: selectedStudentForUnenroll.id,
//           classroom_id: classroom.classroom_id,
//           email: selectedStudentForUnenroll.email,
//           course_selections: unenrollCourses,
//         },
//         {
//           headers: { "X-WP-Nonce": appLocalizer?.nonce },
//         }
//       );

//       if (response.data.success) {
//         alert(__("Unenrollment successful.", "moowoodle"));
//         await fetchClassroomData(currentPage);
//         await fetchClassroomCourses();
//         setShowUnenrollModal(false);
//         setUnenrollCourses([]);
//       } else {
//         alert(__("Unenrollment failed: ", "moowoodle") + response.data.message);
//       }
//     } catch (error) {
//       console.error("Error during unenroll:", error);
//       alert(__("Something went wrong. Please try again.", "moowoodle"));
//     } finally {
//       setIsLoading(false);
//     }
//   };

//   const handleUnenrollStudentCohortAndGroup = async (student) => {
//     try {
//       setIsLoading(true);
//       let payload = {
//         email: student.email
//       };

//       if ( student?.group?.group_id ) {
//         payload = {
//           ...payload,
//           type: 'group',
//           group_id: student.group.group_id,
//           group_item_id: student.group.group_item_id,
//           moodle_group_id: student.group.moodle_group_id
//         };

//       } else if ( student?.cohort?.cohort_id ) {
//         payload = {
//           ...payload,
//           type: 'cohort',
//           cohort_id: student.cohort.cohort_id,
//           group_item_id: student.cohort.group_item_id,
//           moodle_cohort_id: student.cohort.moodle_cohort_id
//         };
//       } else {
//         alert(__("This action is only available for groups or cohorts.", "moowoodle"));
//         return;
//       }

//       const response = await axios.post(
//         getApiLink("unenroll"),
//         payload,
//         {
//           headers: { "X-WP-Nonce": appLocalizer?.nonce },
//         }
//       );

//       if (response.data.success) {
//         alert(__("Unenrollment successful.", "moowoodle"));
//         await fetchClassroomData(currentPage);
//         await fetchClassroomCourses();
//         setShowUnenrollModal(false);
//         setUnenrollCourses([]);
//       } else {
//         alert(__("Unenrollment failed: ", "moowoodle") + response.data.message);
//       }
//     } catch (error) {
//       console.error("Error during unenroll:", error);
//       alert(__("Something went wrong. Please try again.", "moowoodle"));
//     } finally {
//       setIsLoading(false);
//     }
//   };

//   const handlePageChange = (page) => {
//     if (page >= 1 && page <= totalPages) {
//       setCurrentPage(page);
//     }
//   };

//   const downloadSampleCsv = () => {
//     const sampleCsvContent = `first_name,last_name,email\nJohn,Doe,john@example.com\nJane,Smith,jane@example.com`;
//     const blob = new Blob([sampleCsvContent], { type: "text/csv" });
//     const url = window.URL.createObjectURL(blob);
//     const link = document.createElement("a");
//     link.href = url;
//     link.download = "sample-enrollment.csv";
//     document.body.appendChild(link);
//     link.click();
//     document.body.removeChild(link);
//     window.URL.revokeObjectURL(url);
//   };

//   return (
//     <div className="enrollment-container">
//       <h4 className="heading">
//         You are viewing {classroom?.group_name || classroom?.cohort_name || classroom?.classroom_name || "Classroom"}
//       </h4>

//       <div className="class-details">
//         <div className="details-box">
//           <span className="number">{totalCourses}</span>
//           <span className="text">Total Course</span>
//         </div>
//         <div className="details-box">
//           <span className="number">{totalEnrolled}</span>
//           <span className="text">Enrolled</span>
//         </div>
//         <div className="details-box">
//           <span className="number">{totalAvailable}</span>
//           <span className="text">Available</span>
//         </div>
//       </div>

//       {classroom?.classroom_id && (
//         <>
//           <div className="course-title title">Course Details</div>
//           <div className="courses">
//             {availableCourses.map((course, index) => (
//               <div className="course" key={index}>
//                 <img
//                   src={course.image_url || defaultImageUrl}
//                   alt={course.course_name}
//                 />
//                 <div className="course-name">
//                   {course.course_name} (
//                   {course.total_quantity - course.available_quantity}/
//                   {course.total_quantity})
//                 </div>
//               </div>
//             ))}
//           </div>
//         </>
//       )}

//       <div className="course-title title">
//         Enrolled Student
//         <div className="button-group">
//           <button
//             className={`enroll-button bulk-enroll-button ${showForm ? "btn-red" : "btn-green"
//               }`}
//             onClick={() => {
//               setShowForm((prev) => !prev);
//               setShowBulkModal(false);
//             }}
//           >
//             {showForm
//               ? __("Cancel", "moowoodle")
//               : "+ " + __("Enroll Student", "moowoodle")}
//           </button>
//           <button
//             className={`enroll-button bulk-enroll-button ${showBulkModal ? "btn-red" : "btn-green"
//               }`}
//             onClick={() => {
//               setShowBulkModal((prev) => !prev);
//               setShowForm(false);
//             }}
//           >
//             {showBulkModal
//               ? __("Cancel", "moowoodle")
//               : __("Bulk Enroll (CSV)", "moowoodle")}
//           </button>
//         </div>
//       </div>

//       {showForm && (
//         <form className="enroll-form" onSubmit={handleEnrollStudent}>
//           <input
//             type="text"
//             name="first_name"
//             placeholder={__("First Name", "moowoodle")}
//             value={newStudent.first_name}
//             onChange={handleInputChange}
//             required
//           />
//           <input
//             type="text"
//             name="last_name"
//             placeholder={__("Last Name", "moowoodle")}
//             value={newStudent.last_name}
//             onChange={handleInputChange}
//             required
//           />
//           <input
//             type="email"
//             name="email"
//             placeholder={__("Student Email", "moowoodle")}
//             value={newStudent.email}
//             onChange={handleInputChange}
//             required
//           />

//           {classroom.classroom_id && (
//             <Select
//               isMulti
//               options={courseOptions}
//               placeholder={__("Select Courses", "moowoodle")}
//               value={courseOptions.filter((option) =>
//                 newStudent.courses.some(
//                   (course) => course.course_id === option.value
//                 )
//               )}
//               onChange={handleCourseChange}
//             />
//           )}
//           <div className="btn-group">
//             <button
//               type="submit"
//               className="save-button btn-green"
//               disabled={isLoading}
//             >
//               {isLoading
//                 ? __("Enrolling...", "moowoodle")
//                 : __("Enroll", "moowoodle")}
//             </button>
//           </div>
//         </form>
//       )}

//       {showBulkModal && (
//         <form className="enroll-form" onSubmit={handleCsvUpload}>
//           <input
//             className="file-input"
//             type="file"
//             accept=".csv"
//             onChange={(e) => setCsvFile(e.target.files[0])}
//             required
//           />
//           <div className="btn-group">
//             <button
//               type="button"
//               className="btn-green save-button download-sample-button"
//               onClick={downloadSampleCsv}
//             >
//               {__("Download Sample CSV", "moowoodle")}
//             </button>
//             <button
//               type="submit"
//               className="btn-green save-button"
//               disabled={isLoading}
//             >
//               {isLoading
//                 ? __("Uploading...", "moowoodle")
//                 : __("Upload CSV", "moowoodle")}
//             </button>
//           </div>
//         </form>
//       )}

//       {!isLoading && (
//         <table className="woocommerce-orders-table woocommerce-MyAccount-orders shop_table">
//           <thead>
//             <tr>
//               <th className="woocommerce-orders-table__header">Name</th>
//               <th className="woocommerce-orders-table__header">Email</th>
//               <th className="woocommerce-orders-table__header">Enrollment</th>
//               <th className="woocommerce-orders-table__header">Action</th>
//             </tr>
//           </thead>
//           <tbody>
//             {enrolledStudents.length > 0 ? (
//               enrolledStudents.map((student, index) => (
//                 <tr key={index} className="woocommerce-orders-table__row">
//                   <td className="woocommerce-orders-table__cell">{student.name || "N/A"}</td>
//                   <td className="woocommerce-orders-table__cell">{student.email || "N/A"}</td>
//                   <td className="woocommerce-orders-table__cell">
//                     {student.courses?.length > 0
//                       ? student.courses
//                         .map((course) =>
//                           typeof course.course_name === "string" ? course.course_name : "Unknown Course"
//                         )
//                         .join(", ")
//                       : typeof student.group?.name === "string"
//                         ? student.group.name
//                         : typeof student.cohort?.name === "string"
//                           ? student.cohort.name
//                           : "No courses, group, or cohort assigned"}
//                   </td>
//                   <td className="woocommerce-orders-table__cell">
//                     <button
//                       className="unenroll-button btn-red"
//                       onClick={() => {
//                         if (classroom?.group_id || classroom?.cohort_id) {
//                           handleUnenrollStudentCohortAndGroup(student);
//                         } else if (classroom?.classroom_id) {
//                           setSelectedStudentForUnenroll(student);
//                           setShowUnenrollModal(true);
//                         }
//                       }}
//                     >
//                       {__("Unenroll", "moowoodle")}
//                     </button>
//                   </td>
//                 </tr>
//               ))
//             ) : (
//               <tr>
//                 <td colSpan={4}>{__("No students enrolled yet.", "moowoodle")}</td>
//               </tr>
//             )}
//           </tbody>
//         </table>
//       )}

//       {showUnenrollModal && selectedStudentForUnenroll && (
//         <div className="modal-overlay">
//           <div className="modal-content">
//             <h4 className="title">{__("Unenroll Student", "moowoodle")}</h4>
//             <p>
//               {__("Select courses to unenroll", "moowoodle")} -{" "}
//               <strong>{selectedStudentForUnenroll.name}</strong>
//             </p>
//             <Select
//               isMulti
//               options={selectedStudentForUnenroll.courses.map((course) => ({
//                 value: course.course_id,
//                 label: course.course_name,
//                 group_item_id: course.group_item_id,
//                 moodle_course_id: course.moodle_course_id,
//               }))}
//               placeholder={__("Select Courses", "moowoodle")}
//               onChange={(selectedOptions) => {
//                 const courses =
//                   selectedOptions?.map((option) => ({
//                     course_id: option.value,
//                     group_item_id: option.group_item_id,
//                     moodle_course_id:option.moodle_course_id,
//                   })) || [];
//                 setUnenrollCourses(courses);
//               }}
//             />
//             <div className="btn-group">
//               <button
//                 className="cancel-button btn-red"
//                 onClick={() => setShowUnenrollModal(false)}
//               >
//                 {__("Cancel", "moowoodle")}
//               </button>
//               <button
//                 className="save-button btn-green"
//                 onClick={handleUnenrollStudent}
//                 disabled={isLoading}
//               >
//                 {isLoading
//                   ? __("Processing...", "moowoodle")
//                   : __("Confirm Unenroll", "moowoodle")}
//               </button>
//             </div>
//           </div>
//         </div>
//       )}

//       {totalPages > 1 && !isLoading && (
//         <div className="pagination">
//           <button
//             onClick={() => handlePageChange(currentPage - 1)}
//             disabled={currentPage === 1}
//             className="pagination-button"
//           >
//             {__("Previous", "moowoodle")}
//           </button>
//           <span className="pagination-info">
//             {__("Page", "moowoodle")} {currentPage} {__("of", "moowoodle")}{" "}
//             {totalPages}
//           </span>
//           <button
//             onClick={() => handlePageChange(currentPage + 1)}
//             disabled={currentPage === totalPages}
//             className="pagination-button"
//           >
//             {__("Next", "moowoodle")}
//           </button>
//         </div>
//       )}
//     </div>
//   );
// };

// export default ViewEnroll;


import React, { useState, useEffect, useCallback } from "react";
import { __ } from "@wordpress/i18n";
import axios from "axios";
import Select from "react-select";
import { getApiLink } from "../../services/apiService";
import "./MyClassroom.scss";

const ViewEnroll = ({ classroom, onBack }) => {
  const [enrolledStudents, setEnrolledStudents] = useState([]);
  const [availableCourses, setAvailableCourses] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);
  const [newStudent, setNewStudent] = useState({
    first_name: "",
    last_name: "",
    email: "",
    courses: [],
  });
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [csvFile, setCsvFile] = useState(null);
  const [showBulkModal, setShowBulkModal] = useState(false);
  const [totalCourses, setTotalCourses] = useState(0);
  const [totalEnrolled, setTotalEnrolled] = useState(0);
  const [totalAvailable, setTotalAvailable] = useState(0);
  const [showUnenrollModal, setShowUnenrollModal] = useState(false);
  const [selectedStudentForUnenroll, setSelectedStudentForUnenroll] = useState(null);
  const [unenrollCourses, setUnenrollCourses] = useState([]);

  const studentsPerPage = 5;
  const defaultImageUrl =
    "https://cus.dualcube.com/mvx2/wp-content/uploads/2025/04/beanie-2-3-416x416.jpg";

  // Memoized fetch functions
  const fetchClassroomCourses = useCallback(async () => {
    if (classroom?.type !== "classroom") return;

    try {
      const response = await axios.get(getApiLink("view-classroom"), {
        params: {
          type: classroom.type,
          classroom_id: classroom.classroom_id,
        },
        headers: { "X-WP-Nonce": appLocalizer?.nonce },
      });

      if (response.data.success) {
        const { courses = [], total_courses = 0 } = response.data;
        const totalAvailable = courses.reduce(
          (sum, course) => sum + Number(course.available_quantity || 0),
          0
        );

        setAvailableCourses(courses);
        setTotalCourses(total_courses);
        setTotalAvailable(totalAvailable);
      } else {
        setError(__("Failed to load courses: ", "moowoodle") + response.data.message);
      }
    } catch (error) {
      setError(__("Error fetching courses. Please try again.", "moowoodle"));
      console.error("Fetch courses error:", error);
    }
  }, [classroom]);

  const fetchClassroomData = useCallback(
    async (page = 1) => {
      try {
        const payload = classroom?.type === "classroom"
          ? { classroom_type: classroom.type, classroom_id: classroom.classroom_id }
          : { classroom_type: classroom.type, group_item_id: classroom.group_item_id };

        const response = await axios.get(getApiLink("enrollments"), {
          params: { ...payload, page, per_page: studentsPerPage },
          headers: { "X-WP-Nonce": appLocalizer?.nonce },
        });

        if (response.data.success) {
          setEnrolledStudents(response.data.data.students || []);
          setTotalEnrolled(response.data.data.total_enrolled || 0);
          setTotalPages(response.data.pagination?.total_pages || 1);
          setCurrentPage(response.data.pagination?.current_page || page);
        } else {
          setError(__("Failed to load data: ", "moowoodle") + response.data.message);
        }
      } catch (error) {
        setError(__("Error fetching data. Please try again.", "moowoodle"));
        console.error("Fetch data error:", error);
      }
    },
    [classroom]
  );

  useEffect(() => {
    setError(null);
    setIsLoading(true);
    Promise.all([fetchClassroomCourses(), fetchClassroomData(currentPage)])
      .finally(() => setIsLoading(false));
  }, [fetchClassroomCourses, fetchClassroomData, currentPage]);

  const courseOptions = availableCourses.map((item) => ({
    value: item.course_id,
    label: item.course_name,
    item_id: item.item_id,
    group_item_id: item.group_item_id,
  }));

  const handleInputChange = (e) => {
    setNewStudent({ ...newStudent, [e.target.name]: e.target.value });
  };

  const handleCourseChange = (selectedOptions) => {
    const courses = selectedOptions?.map((option) => ({
      course_id: option.value,
      item_id: option.item_id,
      group_item_id: option.group_item_id,
      course_name: option.label,
    })) || [];
    setNewStudent({ ...newStudent, courses });
  };

  const handleEnrollStudent = async (e) => {
    e.preventDefault();
    const { first_name, last_name, email, courses } = newStudent;
    const { type, classroom_id, order_id = 0, cohort_id, item_id, group_id, group_item_id } = classroom;

    if (!first_name || !last_name || !email || (type === "classroom" && !courses.length)) {
      alert(__("Please fill in all required fields.", "moowoodle"));
      return;
    }

    setIsLoading(true);
    setError(null);

    const payload = {
      type,
      first_name,
      last_name,
      email,
      ...(type === "classroom" && {
        classroom_id,
        order_id,
        course_selections: courses.map(({ course_id, item_id, group_item_id }) => ({
          course_id,
          item_id,
          classroom_item_id: group_item_id,
        })),
      }),
      ...(type === "cohort" && { cohort_id, order_id, item_id, cohort_item_id: group_item_id }),
      ...(type === "group" && { group_id, order_id, item_id, group_item_id }),
    };

    try {
      const response = await axios.post(getApiLink("enroll"), payload, {
        headers: { "X-WP-Nonce": appLocalizer?.nonce },
      });

      if (response.data.success) {
        setShowForm(false);
        setNewStudent({ first_name: "", last_name: "", email: "", courses: [] });
        await Promise.all([fetchClassroomData(1), fetchClassroomCourses()]);
        alert(__("Enrollment successful!", "moowoodle"));
      } else {
        setError(__("Enrollment failed: ", "moowoodle") + (response.data.message || "Unknown error"));
      }
    } catch (error) {
      setError(__("Error enrolling student. Please try again.", "moowoodle"));
      console.error("Enrollment error:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const handleCsvUpload = async (e) => {
    e.preventDefault();
    if (!csvFile) {
      alert(__("Please select a CSV file.", "moowoodle"));
      return;
    }

    const { type, classroom_id, order_id = 0, cohort_id, item_id, group_id, group_item_id } = classroom;
    if (type === "classroom" && !availableCourses.length) {
      alert(__("No courses available for enrollment.", "moowoodle"));
      return;
    }

    setIsLoading(true);
    setError(null);

    const payload = { type, order_id };
    if (type === "classroom") {
      payload.classroom_id = classroom_id;
      payload.course_selections = availableCourses.map((course) => ({
        course_id: String(course.course_id),
        item_id: String(course.item_id),
        group_item_id: String(course.group_item_id || 0),
      }));
    } else if (type === "cohort") {
      payload.cohort_id = cohort_id;
      payload.item_id = item_id;
      payload.cohort_item_id = group_item_id;
    } else if (type === "group") {
      payload.group_id = group_id;
      payload.item_id = item_id;
      payload.group_item_id = group_item_id;
    }

    const formData = new FormData();
    formData.append("file", csvFile);
    Object.entries(payload).forEach(([key, value]) => {
      formData.append(key, typeof value === "object" ? JSON.stringify(value) : value);
    });

    try {
      const response = await axios.post(getApiLink("bulk-enroll"), formData, {
        headers: {
          "X-WP-Nonce": appLocalizer?.nonce,
          "Content-Type": "multipart/form-data",
        },
      });

      if (response.data.success) {
        setCsvFile(null);
        setShowBulkModal(false);
        await Promise.all([fetchClassroomData(1), fetchClassroomCourses()]);
        alert(__("Bulk enrollment successful!", "moowoodle"));
      } else {
        setError(__("Bulk enrollment failed: ", "moowoodle") + (response.data.message || "Unknown error"));
      }
    } catch (error) {
      setError(__("Error during bulk enrollment. Please try again.", "moowoodle"));
      console.error("Bulk enrollment error:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const handleUnenroll = async () => {
    if (!unenrollCourses.length) {
      alert(__("Please select at least one course to unenroll.", "moowoodle"));
      return;
    }

    setIsLoading(true);
    setError(null);

    try {
      const response = await axios.post(
        getApiLink("unenroll"),
        {
          type: "course",
          user_id: selectedStudentForUnenroll.id,
          classroom_id: classroom.classroom_id,
          email: selectedStudentForUnenroll.email,
          course_selections: unenrollCourses,
        },
        { headers: { "X-WP-Nonce": appLocalizer?.nonce } }
      );

      if (response.data.success) {
        await Promise.all([fetchClassroomData(currentPage), fetchClassroomCourses()]);
        setShowUnenrollModal(false);
        setUnenrollCourses([]);
        alert(__("Unenrollment successful!", "moowoodle"));
      } else {
        setError(__("Unenrollment failed: ", "moowoodle") + (response.data.message || "Unknown error"));
      }
    } catch (error) {
      setError(__("Error unenrolling student. Please try again.", "moowoodle"));
      console.error("Unenroll error:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const handleUnenrollCohortOrGroup = async (student) => {
    if (!student.group?.group_id && !student.cohort?.cohort_id) {
      alert(__("Unenrollment only available for groups or cohorts.", "moowoodle"));
      return;
    }

    setIsLoading(true);
    setError(null);

    const payload = {
      email: student.email,
      type: student.group?.group_id ? "group" : "cohort",
      ...(student.group?.group_id && {
        group_id: student.group.group_id,
        group_item_id: student.group.group_item_id,
        moodle_group_id: student.group.moodle_group_id,
      }),
      ...(student.cohort?.cohort_id && {
        cohort_id: student.cohort.cohort_id,
        group_item_id: student.cohort.group_item_id,
        moodle_cohort_id: student.cohort.moodle_cohort_id,
      }),
    };

    try {
      const response = await axios.post(getApiLink("unenroll"), payload, {
        headers: { "X-WP-Nonce": appLocalizer?.nonce },
      });

      if (response.data.success) {
        await Promise.all([fetchClassroomData(currentPage), fetchClassroomCourses()]);
        setShowUnenrollModal(false);
        alert(__("Unenrollment successful!", "moowoodle"));
      } else {
        setError(__("Unenrollment failed: ", "moowoodle") + (response.data.message || "Unknown error"));
      }
    } catch (error) {
      setError(__("Error unenrolling student. Please try again.", "moowoodle"));
      console.error("Unenroll error:", error);
    } finally {
      setIsLoading(false);
    }
  };

  const handlePageChange = (page) => {
    if (page >= 1 && page <= totalPages) {
      setCurrentPage(page);
    }
  };

  const downloadSampleCsv = () => {
    const sampleCsvContent = `first_name,last_name,email\nJohn,Doe,john@example.com\nJane,Smith,jane@example.com`;
    const blob = new Blob([sampleCsvContent], { type: "text/csv" });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = "sample-enrollment.csv";
    link.click();
    window.URL.revokeObjectURL(url);
  };

  return (
    <div className="enroll-container">
      <header className="enroll-header">
        <h1>
          {__("Viewing", "moowoodle")} {classroom?.group_name || classroom?.cohort_name || classroom?.classroom_name || __("Classroom", "moowoodle")}
        </h1>
        <button className="back-button" onClick={onBack} disabled={isLoading}>
          {__("Back", "moowoodle")}
        </button>
      </header>

      {error && (
        <div className="error-message" role="alert">
          {error}
          <button className="retry-button" onClick={() => fetchClassroomData(currentPage)}>
            {__("Retry", "moowoodle")}
          </button>
        </div>
      )}

      <div className="stats-grid">
        <div className="stat-box">
          <span className="stat-number">{totalCourses}</span>
          <span className="stat-label">{__("Total Courses", "moowoodle")}</span>
        </div>
        <div className="stat-box">
          <span className="stat-number">{totalEnrolled}</span>
          <span className="stat-label">{__("Enrolled", "moowoodle")}</span>
        </div>
        <div className="stat-box">
          <span className="stat-number">{totalAvailable}</span>
          <span className="stat-label">{__("Available", "moowoodle")}</span>
        </div>
      </div>

      {classroom?.classroom_id && (
        <>
          <h2 className="section-title">{__("Course Details", "moowoodle")}</h2>
          <div className="courses-grid">
            {availableCourses.length ? (
              availableCourses.map((course) => (
                <div className="course-card" key={course.item_id}>
                  <img
                    src={course.image_url || defaultImageUrl}
                    alt={course.course_name}
                    className="course-image"
                  />
                  <div className="course-info">
                    <h3>{course.course_name}</h3>
                    <p>
                      {course.total_quantity - course.available_quantity}/{course.total_quantity} {__("enrolled", "moowoodle")}
                    </p>
                  </div>
                </div>
              ))
            ) : (
              <p className="no-data">{__("No courses available.", "moowoodle")}</p>
            )}
          </div>
        </>
      )}

      <section className="enroll-section">
        <h2 className="section-title">
          {__("Enrolled Students", "moowoodle")}
          <div className="action-buttons">
            <button
              className={`action-button ${showForm ? "cancel" : "enroll"}`}
              onClick={() => {
                setShowForm(!showForm);
                setShowBulkModal(false);
              }}
              disabled={isLoading}
            >
              {showForm ? __("Cancel", "moowoodle") : __("Enroll Student", "moowoodle")}
            </button>
            <button
              className={`action-button ${showBulkModal ? "cancel" : "bulk-enroll"}`}
              onClick={() => {
                setShowBulkModal(!showBulkModal);
                setShowForm(false);
              }}
              disabled={isLoading}
            >
              {showBulkModal ? __("Cancel", "moowoodle") : __("Bulk Enroll (CSV)", "moowoodle")}
            </button>
          </div>
        </h2>

        {showForm && (
          <form className="enroll-form" onSubmit={handleEnrollStudent}>
            <input
              type="text"
              name="first_name"
              placeholder={__("First Name", "moowoodle")}
              value={newStudent.first_name}
              onChange={handleInputChange}
              required
              disabled={isLoading}
            />
            <input
              type="text"
              name="last_name"
              placeholder={__("Last Name", "moowoodle")}
              value={newStudent.last_name}
              onChange={handleInputChange}
              required
              disabled={isLoading}
            />
            <input
              type="email"
              name="email"
              placeholder={__("Student Email", "moowoodle")}
              value={newStudent.email}
              onChange={handleInputChange}
              required
              disabled={isLoading}
            />
            {classroom.classroom_id && (
              <Select
                isMulti
                options={courseOptions}
                placeholder={__("Select Courses", "moowoodle")}
                value={courseOptions.filter((option) =>
                  newStudent.courses.some((course) => course.course_id === option.value)
                )}
                onChange={handleCourseChange}
                isDisabled={isLoading}
                className="course-select"
              />
            )}
            <button
              type="submit"
              className="submit-button"
              disabled={isLoading}
            >
              {isLoading ? __("Enrolling...", "moowoodle") : __("Enroll", "moowoodle")}
            </button>
          </form>
        )}

        {showBulkModal && (
          <form className="enroll-form" onSubmit={handleCsvUpload}>
            <input
              type="file"
              accept=".csv"
              onChange={(e) => setCsvFile(e.target.files[0])}
              required
              disabled={isLoading}
              className="file-input"
            />
            <div className="form-actions">
              <button
                type="button"
                className="sample-csv-button"
                onClick={downloadSampleCsv}
                disabled={isLoading}
              >
                {__("Download Sample CSV", "moowoodle")}
              </button>
              <button
                type="submit"
                className="submit-button"
                disabled={isLoading}
              >
                {isLoading ? __("Uploading...", "moowoodle") : __("Upload CSV", "moowoodle")}
              </button>
            </div>
          </form>
        )}

        <table className="enroll-table">
          <thead>
            <tr>
              <th>{__("Name", "moowoodle")}</th>
              <th>{__("Email", "moowoodle")}</th>
              <th>{__("Enrollment", "moowoodle")}</th>
              <th>{__("Action", "moowoodle")}</th>
            </tr>
          </thead>
          <tbody>
            {isLoading && !enrolledStudents.length ? (
              <tr>
                <td colSpan={4} className="loading">{__("Loading...", "moowoodle")}</td>
              </tr>
            ) : enrolledStudents.length ? (
              enrolledStudents.map((student) => (
                <tr key={student.id || student.email}>
                  <td>{student.name || "N/A"}</td>
                  <td>{student.email || "N/A"}</td>
                  <td>
                    {student.courses?.length
                      ? student.courses.map((course) => course.course_name || "Unknown Course").join(", ")
                      : student.group?.name || student.cohort?.name || __("None", "moowoodle")}
                  </td>
                  <td>
                    <button
                      className="unenroll-button"
                      onClick={() => {
                        if (classroom?.group_id || classroom?.cohort_id) {
                          handleUnenrollCohortOrGroup(student);
                        } else {
                          setSelectedStudentForUnenroll(student);
                          setShowUnenrollModal(true);
                        }
                      }}
                      disabled={isLoading}
                    >
                      {__("Unenroll", "moowoodle")}
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan={4}>{__("No students enrolled.", "moowoodle")}</td>
              </tr>
            )}
          </tbody>
        </table>

        {showUnenrollModal && selectedStudentForUnenroll && (
          <div className="modal">
            <div className="modal-content">
              <h3>{__("Unenroll Student", "moowoodle")}</h3>
              <p>
                {__("Select courses to unenroll", "moowoodle")} <strong>{selectedStudentForUnenroll.name}</strong>
              </p>
              <Select
                isMulti
                options={selectedStudentForUnenroll.courses.map((course) => ({
                  value: course.course_id,
                  label: course.course_name,
                  group_item_id: course.group_item_id,
                  moodle_course_id: course.moodle_course_id,
                }))}
                placeholder={__("Select Courses", "moowoodle")}
                onChange={(selectedOptions) => {
                  setUnenrollCourses(
                    selectedOptions?.map((option) => ({
                      course_id: option.value,
                      group_item_id: option.group_item_id,
                      moodle_course_id: option.moodle_course_id,
                    })) || []
                  );
                }}
                isDisabled={isLoading}
                className="course-select"
              />
              <div className="modal-actions">
                <button
                  className="cancel-button"
                  onClick={() => setShowUnenrollModal(false)}
                  disabled={isLoading}
                >
                  {__("Cancel", "moowoodle")}
                </button>
                <button
                  className="submit-button"
                  onClick={handleUnenroll}
                  disabled={isLoading || !unenrollCourses.length}
                >
                  {isLoading ? __("Processing...", "moowoodle") : __("Unenroll", "moowoodle")}
                </button>
              </div>
            </div>
          </div>
        )}

        {totalPages > 1 && (
          <nav className="pagination" aria-label={__("Pagination", "moowoodle")}>
            <button
              onClick={() => handlePageChange(currentPage - 1)}
              disabled={currentPage === 1 || isLoading}
              aria-label={__("Previous page", "moowoodle")}
            >
              {__("Previous", "moowoodle")}
            </button>
            <span aria-live="polite">
              {__("Page", "moowoodle")} {currentPage} {__("of", "moowoodle")} {totalPages}
            </span>
            <button
              onClick={() => handlePageChange(currentPage + 1)}
              disabled={currentPage === totalPages || isLoading}
              aria-label={__("Next page", "moowoodle")}
            >
              {__("Next", "moowoodle")}
            </button>
          </nav>
        )}
      </section>
    </div>
  );
};

export default ViewEnroll;