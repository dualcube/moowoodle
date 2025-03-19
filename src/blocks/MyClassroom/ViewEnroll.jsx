import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import axios from "axios";
import Select from "react-select";
import { getApiLink } from "../../services/apiService";
import "./MyClassroom.scss";
import CourseCard from "./CourseCard";

const ViewEnroll = ({ classroom, onBack }) => {
    const [enrolledStudents, setEnrolledStudents] = useState([]);
    const [availableCourses, setAvailableCourses] = useState([]);
    const [showForm, setShowForm] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [newStudent, setNewStudent] = useState({ name: "", email: "", courses: [] });
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [csvFile, setCsvFile] = useState(null);
    const [showBulkModal, setShowBulkModal] = useState(false);
    const studentsPerPage = 5;

    const fetchClassroomData = async (page = 1) => {
        try {
            let response = await axios.get(getApiLink("view"), {
                params: { group_id: classroom.group_id, page, per_page: studentsPerPage },
                headers: { "X-WP-Nonce": appLocalizer?.nonce },
            });
            response = response.data;
            setAvailableCourses(response.data.items || []);
            setEnrolledStudents(response.data.enrollments || []);
            setTotalPages(response.data.total_pages || 1);
            setCurrentPage(response.data.current_page || page);
        } catch (error) {
            console.error(__("Error fetching classroom data:", "moowoodle-pro"), error);
        }
    };

    useEffect(() => {
        fetchClassroomData(currentPage);
    }, [currentPage]);

    const courseOptions = availableCourses.map((item) => ({
        value: item.course_id,
        label: item.course_name,
        group_item_id: item.id,
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
        if (!newStudent.name || !newStudent.email || !newStudent.courses.length) {
            alert(__("Please fill in all fields.", "moowoodle-pro"));
            return;
        }

        setIsLoading(true);
        const payload = {
            group_id: classroom.group_id,
            email: newStudent.email,
            name: newStudent.name,
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
                setNewStudent({ name: "", email: "", courses: [] });
                await fetchClassroomData(1);
                alert(__("Enrollment successful! The classroom data has been updated.", "moowoodle-pro"));
            } else {
                alert(__("Enrollment failed: ", "moowoodle-pro") + (response.data.message || __("Unknown error", "moowoodle-pro")));
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
                await fetchClassroomData(1);
                alert(__("Bulk enrollment successful!", "moowoodle-pro"));
            } else {
                alert(__("Bulk enrollment failed: ", "moowoodle-pro") + (response.data.message || __("Unknown error", "moowoodle-pro")));
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
            fetchClassroomData(page);
        }
    };

    const downloadSampleCsv = () => {
        const sampleCsvContent = `name,email
    John Doe,john@example.com
    Jane Smith,jane@example.com`;
        
        const blob = new Blob([sampleCsvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'sample-enrollment.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    };
    return (
        <div className="enrollment-container">
            <button className="back-button" onClick={onBack}>← {__("Back to Classrooms", "moowoodle-pro")}</button>

            <div className="course-grid">
                {availableCourses.map((course) => (
                    <CourseCard key={course.course_id} course={course} />
                ))}
            </div>

            <h1>{__("Enrolled Students for", "moowoodle-pro")} {classroom.group_name}</h1>

            <div className="button-group">
                <button className="enroll-button" onClick={() => setShowForm(!showForm)}>
                    {showForm ? __("Cancel", "moowoodle-pro") : "+ " + __("Enroll Student", "moowoodle-pro")}
                </button>
                <button 
                    className="enroll-button bulk-enroll-button" 
                    onClick={() => setShowBulkModal(!showBulkModal)}
                >
                    {showBulkModal ? __("Cancel", "moowoodle-pro") : __("Bulk Enroll (CSV)", "moowoodle-pro")}
                </button>
            </div>

            {showForm && (
                <form className="enroll-form" onSubmit={handleEnrollStudent}>
                    <input
                        type="text"
                        name="name"
                        placeholder={__("Student Name", "moowoodle-pro")}
                        value={newStudent.name}
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
                    <button type="submit" className="save-button" disabled={isLoading}>
                        {isLoading ? __("Enrolling...", "moowoodle-pro") : __("Enroll", "moowoodle-pro")}
                    </button>
                </form>
            )}

            {showBulkModal && (
                <form className="enroll-form" onSubmit={handleCsvUpload}>
                    <button 
                        type="button" 
                        className="save-button download-sample-button"
                        onClick={downloadSampleCsv}
                    >
                        {__("Download Sample CSV", "moowoodle-pro")}
                    </button>
                    <input
                        type="file"
                        accept=".csv"
                        onChange={(e) => setCsvFile(e.target.files[0])}
                        required
                    />
                    <button 
                        type="submit" 
                        className="save-button"
                        disabled={isLoading}
                    >
                        {isLoading ? __("Uploading...", "moowoodle-pro") : __("Upload CSV", "moowoodle-pro")}
                    </button>
                </form>
            )}

            <div className="student-list">
                {enrolledStudents.length > 0 ? (
                    enrolledStudents.map((student, index) => (
                        <div key={index} className="student-card">
                            <h2>{student.email}</h2>
                            <p><strong>{__("Email:", "moowoodle-pro")}</strong> {student.email}</p>
                            <p><strong>{__("Enrolled Courses:", "moowoodle-pro")}</strong></p>
                            <ul>
                                {student.courses.map((course, idx) => (
                                    <li key={idx}>{course.course_name} - <i>{course.date}</i></li>
                                ))}
                            </ul>
                        </div>
                    ))
                ) : (
                    <p>{__("No students enrolled yet.", "moowoodle-pro")}</p>
                )}
            </div>

            {totalPages > 1 && (
                <div className="pagination">
                    <button onClick={() => handlePageChange(currentPage - 1)} disabled={currentPage === 1}>
                        {__("Previous", "moowoodle-pro")}
                    </button>
                    <span>{__("Page", "moowoodle-pro")} {currentPage} {__("of", "moowoodle-pro")} {totalPages}</span>
                    <button onClick={() => handlePageChange(currentPage + 1)} disabled={currentPage === totalPages}>
                        {__("Next", "moowoodle-pro")}
                    </button>
                </div>
            )}
        </div>
    );
};

export default ViewEnroll;