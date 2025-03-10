import React, { useEffect, useState } from "react";
import axios from "axios";
import { getApiLink } from "../../services/apiService";

const MyCourse = () => {
  const [courses, setCourses] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const perPage = 5;

  useEffect(() => {
    fetchCourses(currentPage);
  }, [currentPage]);

  const fetchCourses = async (page) => {
    if (!page || page < 1) return; // Prevent invalid API calls

    setLoading(true);
    setError(null);

    try {
      const response = await axios.post(
        getApiLink("get-user-courses"),
        { page, row: perPage },
        { headers: { "X-WP-Nonce": appLocalizer.nonce } }
      );

      if (response?.data?.courses) {
        setCourses(response.data.courses);
        setTotalPages(response.data.total_pages || 1);
      } else {
        setCourses([]); // Handle unexpected empty response
        setTotalPages(1);
      }
    } catch (err) {
      console.error("Error fetching courses:", err);
      setError("Failed to load courses. Please try again.");
      setCourses([]);
    }

    setLoading(false);
  };

  return (
    <div className="auto">
      {courses.length > 0 && <p>Total Courses: {courses.length}</p>}

      <table className="moowoodle-table shop_table shop_table_responsive my_account_orders">
        <thead>
          <tr>
            <th>Username</th>
            <th>Course Name</th>
            <th>Enrolment Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          {loading ? (
            <tr>
              <td colSpan="4" className="loading-row">Loading...</td>
            </tr>
          ) : error ? (
            <tr>
              <td colSpan="4" className="error-row">{error}</td>
            </tr>
          ) : courses.length > 0 ? (
            courses.map((course, index) => (
              <tr key={index}>
                <td>{course.user_login || "N/A"}</td> 
                <td>{course.course_name || "Unknown Course"}</td>
                <td>{course.enrolment_date || "No Date Available"}</td> 
                <td>
                  {course.moodle_url ? (
                    <a
                      target="_blank"
                      rel="noopener noreferrer"
                      className="woocommerce-button wp-element-button moowoodle"
                      href={course.moodle_url}
                    >
                      View
                    </a>
                  ) : (
                    <span className="disabled">No Link</span>
                  )}
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="4" className="no-data-row">You haven't purchased any courses yet.</td>
            </tr>
          )}
        </tbody>
      </table>

      {/* Pagination Controls */}
      {totalPages > 1 && (
        <div className="pagination">
          <button disabled={currentPage === 1} onClick={() => setCurrentPage((prev) => Math.max(prev - 1, 1))}>
            Previous
          </button>
          <span>
            Page {currentPage} of {totalPages}
          </span>
          <button disabled={currentPage === totalPages} onClick={() => setCurrentPage((prev) => Math.min(prev + 1, totalPages))}>
            Next
          </button>
        </div>
      )}
    </div>
  );
};

export default MyCourse;
