import React, { useEffect, useState, useCallback } from "react";
import axios from "axios";
import { getApiLink } from "../../services/apiService";

const MyCourse = () => {
  const [courses, setCourses] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const perPage = 5;

  // Memoize fetchCourses to prevent unnecessary re-renders
  const fetchCourses = useCallback(async (page) => {
    if (!page || page < 1) return;
    
    setLoading(true);
    setError(null);
    
    try {
      const response = await axios.get(getApiLink("courses"), {
        params: { page, row: perPage },
        headers: { "X-WP-Nonce": appLocalizer.nonce },
      });
      
      const newCourses = response?.data?.courses || [];
      setCourses(newCourses);
      setTotalPages(response.data.total_pages || 1);
    } catch (err) {
      console.error("Error fetching courses:", err);
      setError("Failed to load courses. Please try again.");
      setCourses([]);
    } finally {
      setLoading(false);
    }
  }, []); 

  useEffect(() => {
    fetchCourses(currentPage);
  }, [currentPage, fetchCourses]);

  const renderTableContent = () => {
    if (loading) {
      return (
        <tr>
          <td colSpan="4" className="loading-row">Loading...</td>
        </tr>
      );
    }
    
    if (error) {
      return (
        <tr>
          <td colSpan="4" className="error-row">{error}</td>
        </tr>
      );
    }
    
    if (courses.length === 0) {
      return (
        <tr>
          <td colSpan="4" className="no-data-row">You haven't purchased any courses yet.</td>
        </tr>
      );
    }
    
    return courses.map((course, index) => (
      <tr key={course.id || index}> 
        <td data-label="Username">{course.user_login || "N/A"}</td>
        <td data-label="Course Name">{course.course_name || "Unknown Course"}</td>
        <td data-label="Enrolment Date">{course.enrolment_date || "No Date Available"}</td>
        <td data-label="Action">
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
    ));
  };

  const renderPagination = () => {
    if (totalPages <= 1) return null;
    
    return (
      <div className="pagination">
        <button
          disabled={currentPage === 1 || loading}
          onClick={() => setCurrentPage((prev) => Math.max(prev - 1, 1))}
        >
          Previous
        </button>
        <span>
          Page {currentPage} of {totalPages}
        </span>
        <button
          disabled={currentPage === totalPages || loading}
          onClick={() => setCurrentPage((prev) => Math.min(prev + 1, totalPages))}
        >
          Next
        </button>
      </div>
    );
  };

  return (
    <div className="auto">
      <table className="moowoodle-table shop_table shop_table_responsive my_account_orders">
        <thead>
          <tr>
            <th>Username</th>
            <th>Course Name</th>
            <th>Enrolment Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>{renderTableContent()}</tbody>
      </table>

      {renderPagination()}
    </div>
  );
};

export default MyCourse;