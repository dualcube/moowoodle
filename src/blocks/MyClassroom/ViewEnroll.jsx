import React, { useState, useEffect } from "react";
import axios from "axios";
import Select from "react-select";
import { getApiLink } from "../../services/apiService";
import "./MyClassroom.scss";
import CourseCard from "./CourseCard";

const ViewEnroll = ({ classroom, onBack }) => {
    const [enrolledStudents, setEnrolledStudents] = useState([]);
    const [availableCourses, setAvailableCourses] = useState([]); // Store courses from API
    const [showForm, setShowForm] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [newStudent, setNewStudent] = useState({ name: "", email: "", courses: [] });
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const studentsPerPage = 5;

    // Fetch data from API (both students and available courses)
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
            console.error("Error fetching classroom data:", error);
        }
    };

    useEffect(() => {
        fetchClassroomData(currentPage);
    }, [currentPage]);

    // Convert fetched courses into options for Select
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
            alert("Please fill in all fields.");
            return;
        }

        setIsLoading(true);

        const payload = {
            group_id:classroom.group_id,
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

                alert("Enrollment successful! The classroom data has been updated.");
            } else {
                alert("Enrollment failed: " + (response.data.message || "Unknown error"));
            }
        } catch (error) {
            console.error("Error enrolling student:", error);
            alert("Error enrolling student. Please try again.");
        }

        setIsLoading(false);
    };

    const handlePageChange = (page) => {
        if (page >= 1 && page <= totalPages) {
            setCurrentPage(page);
            fetchClassroomData(page);
        }
    };

    return (
        <div className="enrollment-container">
            <button className="back-button" onClick={onBack}>← Back to Classrooms</button>
            <button className="back-button">Add Course</button>

            <div className="course-grid">
                {availableCourses.map((course) => (
                    <CourseCard key={course.course_id} course={course} />
                ))}
            </div>

            <h1>Enrolled Students for {classroom.group_name}</h1>

            <button className="enroll-button" onClick={() => setShowForm(!showForm)}>
                {showForm ? "Cancel" : "+ Enroll Student"}
            </button>

            {showForm && (
                <form className="enroll-form" onSubmit={handleEnrollStudent}>
                    <input
                        type="text"
                        name="name"
                        placeholder="Student Name"
                        value={newStudent.name}
                        onChange={handleInputChange}
                        required
                    />
                    <input
                        type="email"
                        name="email"
                        placeholder="Student Email"
                        value={newStudent.email}
                        onChange={handleInputChange}
                        required
                    />
                    <Select
                        isMulti
                        options={courseOptions}
                        placeholder="Select Courses"
                        value={courseOptions.filter((option) =>
                            newStudent.courses.some((course) => course.course_id === option.value)
                        )}
                        onChange={handleCourseChange}
                    />
                    <button type="submit" className="save-button" disabled={isLoading}>
                        {isLoading ? "Enrolling..." : "Enroll"}
                    </button>
                </form>
            )}

            <div className="student-list">
                {enrolledStudents.length > 0 ? (
                    enrolledStudents.map((student, index) => (
                        <div key={index} className="student-card">
                            <h2>{student.email}</h2>
                            <p><strong>Email:</strong> {student.email}</p>
                            <p><strong>Enrolled Courses:</strong></p>
                            <ul>
                                {student.courses.map((course, idx) => (
                                    <li key={idx}>{course.course_name} - <i>{course.date}</i></li>
                                ))}
                            </ul>
                        </div>
                    ))
                ) : (
                    <p>No students enrolled yet.</p>
                )}
            </div>

            {totalPages > 1 && (
                <div className="pagination">
                    <button onClick={() => handlePageChange(currentPage - 1)} disabled={currentPage === 1}>
                        Previous
                    </button>
                    <span>Page {currentPage} of {totalPages}</span>
                    <button onClick={() => handlePageChange(currentPage + 1)} disabled={currentPage === totalPages}>
                        Next
                    </button>
                </div>
            )}
        </div>
    );
};

export default ViewEnroll;
