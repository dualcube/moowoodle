import React, { useState, useEffect } from "react";
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
    const [newStudent, setNewStudent] = useState({ first_name: "", last_name: "", email: "", courses: [] });
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [csvFile, setCsvFile] = useState(null);
    const [showBulkModal, setShowBulkModal] = useState(false);
    const [totalCourses, setTotalCourses] = useState(0);
    const [totalEnrolled, setTotalEnrolled] = useState(0);
    const [totalAvailable, setTotalAvailable] = useState(0);
    const studentsPerPage = 5;

    const fetchClassroomData = async (page = 1) => {
        try {
            let response = await axios.get(getApiLink("view"), {
                params: { group_id: classroom.group_id, page, per_page: studentsPerPage },
                headers: { "X-WP-Nonce": appLocalizer?.nonce },
            });

            response = response.data;

            if (response.success) {
                const { students, courses, total_courses, total_enrolled } = response.data;

                setEnrolledStudents(students || []);
                setAvailableCourses(courses || []);
                setTotalCourses(total_courses || 0);
                setTotalEnrolled(total_enrolled || 0);

                const totalAvailable = (courses || []).reduce(
                    (acc, course) => acc + Number(course.available_quantity || 0),
                    0
                );
                setTotalAvailable(totalAvailable);

                setTotalPages(response.pagination?.total_pages || 1);
                setCurrentPage(response.pagination?.current_page || page);
            } else {
                console.error("Failed to load classroom data:", response.message);
            }
        } catch (error) {
            console.error(__("Error fetching classroom data:", "moowoodle-pro"), error);
        }
    };

    useEffect(() => {
        fetchClassroomData(currentPage);
    }, [currentPage]);

    const courseOptions = availableCourses.map((item) => ({
        value: item.course_id,
        label: item.name,
        group_item_id: item.group_item_id,
    }));

    const handleInputChange = (e) => {
        setNewStudent({ ...newStudent, [e.target.name]: e.target.value });
    };

    const handleCourseChange = (selectedOptions) => {
        const courses = selectedOptions?.map((option) => ({
            course_id: option.value,
            group_item_id: option.group_item_id,
            course_name: option.label,
        })) || [];
        setNewStudent({ ...newStudent, courses });
    };

    const handleEnrollStudent = async (e) => {
        e.preventDefault();
        if (
            !newStudent.first_name ||
            !newStudent.last_name ||
            !newStudent.email ||
            !Array.isArray(newStudent.courses) ||
            !newStudent.courses.length
        ) {
            alert(__("Please fill in all fields.", "moowoodle-pro"));
            return;
        }

        setIsLoading(true);
        const payload = {
            group_id: classroom.group_id,
            email: newStudent.email,
            first_name: newStudent.first_name,
            last_name: newStudent.last_name,
            order_id: classroom?.order_id || 0,
            course_selections: newStudent.courses.map((course) => ({
                course_id: course.course_id,
                group_item_id: course.group_item_id,
            })),
        };
        try {
            const response = await axios.post(getApiLink("enroll"), payload, {
                headers: { "X-WP-Nonce": appLocalizer?.nonce },
            });

            if (response.data.success) {
                setShowForm(false);
                setNewStudent({ first_name: "", last_name: "", email: "", courses: [] });
                await fetchClassroomData(1); // Reset to page 1 after enrollment
                alert(__("Enrollment successful! The classroom data has been updated.", "moowoodle-pro"));
            } else {
                alert(
                    __("Enrollment failed: ", "moowoodle-pro") +
                        (response.data.message || __("Unknown error", "moowoodle-pro"))
                );
            }
        } catch (error) {
            console.error(__("Error enrolling student:", "moowoodle-pro"), error);
            alert(__("Error enrolling student. Please try again.", "moowoodle-pro"));
        }
        setIsLoading(false);
    };

    const handleCsvUpload = async (e) => {
        e.preventDefault();
        if (!csvFile) {
            alert(__("Please select a CSV file.", "moowoodle-pro"));
            return;
        }

        setIsLoading(true);
        const formData = new FormData();
        formData.append("file", csvFile);
        formData.append("group_id", classroom.group_id);
        formData.append("order_id", classroom?.order_id || 0);

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
                await fetchClassroomData(1); // Reset to page 1 after bulk enrollment
                alert(__("Bulk enrollment successful!", "moowoodle-pro"));
            } else {
                alert(
                    __("Bulk enrollment failed: ", "moowoodle-pro") +
                        (response.data.message || __("Unknown error", "moowoodle-pro"))
                );
            }
        } catch (error) {
            console.error(__("Error during bulk enrollment:", "moowoodle-pro"), error);
            alert(__("Error during bulk enrollment. Please try again.", "moowoodle-pro"));
        }

        setIsLoading(false);
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
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    };

    return (
        <div className="enrollment-container">
            <h4 className="heading">You are viewing {classroom?.name || "Classroom"}</h4>

            <div className="class-details">
                <div className="details-box">
                    <span className="number">{totalCourses}</span>
                    <span className="text">Total Course</span>
                </div>
                <div className="details-box">
                    <span className="number">{totalEnrolled}</span>
                    <span className="text">Enrolled</span>
                </div>
                <div className="details-box">
                    <span className="number">{totalAvailable}</span>
                    <span className="text">Available</span>
                </div>
            </div>

            <div className="course-title">Course Details</div>
            <div className="courses">
                {availableCourses.map((course, index) => (
                    <div className="course" key={index}>
                        <img
                            src="https://cus.dualcube.com/mvx2/wp-content/uploads/2025/04/beanie-2-3-416x416.jpg"
                            alt={course.name}
                        />
                        <div className="course-name">
                            {course.name} ({course.total_quantity - course.available_quantity}/{course.total_quantity})
                        </div>
                    </div>
                ))}
            </div>

            <div className="course-title">
                Enrolled Student
                <div className="button-group">
                    <button
                        className="enroll-button"
                        onClick={() => {
                            setShowForm((prev) => !prev);
                            setShowBulkModal(false);
                        }}
                    >
                        {showForm ? __("Cancel", "moowoodle-pro") : "+ " + __("Enroll Student", "moowoodle-pro")}
                    </button>
                    <button
                        className="enroll-button bulk-enroll-button"
                        onClick={() => {
                            setShowBulkModal((prev) => !prev);
                            setShowForm(false);
                        }}
                    >
                        {showBulkModal ? __("Cancel", "moowoodle-pro") : __("Bulk Enroll (CSV)", "moowoodle-pro")}
                    </button>
                </div>
            </div>

            {showForm && (
                <form className="enroll-form" onSubmit={handleEnrollStudent}>
                    <input
                        type="text"
                        name="first_name"
                        placeholder={__("First Name", "moowoodle-pro")}
                        value={newStudent.first_name}
                        onChange={handleInputChange}
                        required
                    />
                    <input
                        type="text"
                        name="last_name"
                        placeholder={__("Last Name", "moowoodle-pro")}
                        value={newStudent.last_name}
                        onChange={handleInputChange}
                        required
                    />
                    <input
                        type="email"
                        name="email"
                        placeholder={__("Student Email", "moowoodle-pro")}
                        value={newStudent.email}
                        onChange={handleInputChange}
                        required
                    />
                    <Select
                        isMulti
                        options={courseOptions}
                        placeholder={__("Select Courses", "moowoodle-pro")}
                        value={courseOptions.filter((option) =>
                            newStudent.courses.some((course) => course.course_id === option.value)
                        )}
                        onChange={handleCourseChange}
                    />
                    <div className="btn-group">
                        <button type="submit" className="save-button" disabled={isLoading}>
                            {isLoading ? __("Enrolling...", "moowoodle-pro") : __("Enroll", "moowoodle-pro")}
                        </button>
                    </div>
                </form>
            )}

            {showBulkModal && (
                <form className="enroll-form" onSubmit={handleCsvUpload}>
                    <input
                        className="file-input"
                        type="file"
                        accept=".csv"
                        onChange={(e) => setCsvFile(e.target.files[0])}
                        required
                    />
                    <div className="btn-group">
                        <button
                            type="button"
                            className="save-button download-sample-button"
                            onClick={downloadSampleCsv}
                        >
                            {__("Download Sample CSV", "moowoodle-pro")}
                        </button>
                        <button
                            type="submit"
                            className="save-button"
                            disabled={isLoading}
                        >
                            {isLoading ? __("Uploading...", "moowoodle-pro") : __("Upload CSV", "moowoodle-pro")}
                        </button>
                    </div>
                </form>
            )}

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Courses</th>
                    </tr>
                </thead>
                <tbody>
                    {enrolledStudents.length > 0 ? (
                        enrolledStudents.map((student, index) => (
                            <tr key={index}>
                                <td>{student.name}</td>
                                <td>{student.email}</td>
                                <td>
                                    {student.courses?.map((course) => course.course_name).join(", ") ||
                                        "No courses assigned"}
                                </td>
                            </tr>
                        ))
                    ) : (
                        <tr>
                            <td colSpan="3">{__("No students enrolled yet.", "moowoodle-pro")}</td>
                        </tr>
                    )}
                </tbody>
            </table>

            {totalPages > 1 && (
                <div className="pagination">
                    <button
                        onClick={() => handlePageChange(currentPage - 1)}
                        disabled={currentPage === 1}
                        className="pagination-button"
                    >
                        {__("Previous", "moowoodle-pro")}
                    </button>
                    <span className="pagination-info">
                        {__("Page", "moowoodle-pro")} {currentPage} {__("of", "moowoodle-pro")} {totalPages}
                    </span>
                    <button
                        onClick={() => handlePageChange(currentPage + 1)}
                        disabled={currentPage === totalPages}
                        className="pagination-button"
                    >
                        {__("Next", "moowoodle-pro")}
                    </button>
                </div>
            )}
        </div>
    );
};

export default ViewEnroll;