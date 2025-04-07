import React, { useEffect, useState, useCallback } from "react";
import { __, sprintf } from "@wordpress/i18n";
import axios from "axios";
import { getApiLink } from "../../services/apiService";

const MyCourse = () => {
  const [courses, setCourses] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const perPage = 5;

  const fetchCourses = useCallback(async (page) => {
    if (!page || page < 1) return;

    setLoading(true);
    setError(null);

    try {
      const response = await axios.get(getApiLink("courses"), {
        params: { page, row: perPage },
        headers: { "X-WP-Nonce": appLocalizer.nonce },
      });

      const data = response?.data?.data || [];
      const pagination = response?.data?.pagination || {};

      setCourses(data);
      setTotalPages(pagination.total_pages || 1);
    } catch (err) {
      console.error("Error fetching courses:", err);
      setError(__("Failed to load courses. Please try again.", "moowoodle"));
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
          <td colSpan="4" className="loading-row">
            {__("Loading...", "moowoodle")}
          </td>
        </tr>
      );
    }

    if (error) {
      return (
        <tr>
          <td colSpan="4" className="error-row">
            {error}
          </td>
        </tr>
      );
    }

    if (courses.length === 0) {
      return (
        <tr>
          <td colSpan="4" className="no-data-row">
            {__("You haven't purchased any courses yet.", "moowoodle")}
          </td>
        </tr>
      );
    }

    return courses.map((course, index) => (
      <tr key={index}>
        <td data-label={__("Username", "moowoodle")}>
          {course.user_name || __("N/A", "moowoodle")}
        </td>
        <td data-label={__("Course Name", "moowoodle")}>
          {course.course_name || __("Unknown Course", "moowoodle")}
        </td>
        <td data-label={__("Enrolment Date", "moowoodle")}>
          {course.enrolment_date || __("No Date Available", "moowoodle")}
        </td>
        <td data-label={__("Action", "moowoodle")}>
          {course.moodle_url ? (
            <a
              target="_blank"
              rel="noopener noreferrer"
              className="woocommerce-button wp-element-button moowoodle"
              href={course.moodle_url}
            >
              {__("View", "moowoodle")}
            </a>
          ) : (
            <span className="disabled">{__("No Link", "moowoodle")}</span>
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
          {__("Previous", "moowoodle")}
        </button>
        <span>
          {sprintf(__("Page %d of %d", "moowoodle"), currentPage, totalPages)}
        </span>
        <button
          disabled={currentPage === totalPages || loading}
          onClick={() => setCurrentPage((prev) => Math.min(prev + 1, totalPages))}
        >
          {__("Next", "moowoodle")}
        </button>
      </div>
    );
  };

  return (
    <div className="auto">
      <table className="moowoodle-table shop_table shop_table_responsive my_account_orders">
        <thead>
          <tr>
            <th>{__("Username", "moowoodle")}</th>
            <th>{__("Course Name", "moowoodle")}</th>
            <th>{__("Enrolment Date", "moowoodle")}</th>
            <th>{__("Action", "moowoodle")}</th>
          </tr>
        </thead>
        <tbody>{renderTableContent()}</tbody>
      </table>

      {renderPagination()}
    </div>
  );
};

export default MyCourse;
