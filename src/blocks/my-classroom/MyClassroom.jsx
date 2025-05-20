import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import axios from "axios";
import { getApiLink } from "../../services/apiService";
import ViewEnroll from "./ViewEnroll";
import "./MyClassroom.scss";

const MyClassroom = () => {
  const [classrooms, setClassrooms] = useState([]);
  const [cohorts, setCohorts] = useState([]);
  const [groups, setGroups] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [selectedClassroom, setSelectedClassroom] = useState(null);
  const [editingClassroom, setEditingClassroom] = useState(null);
  const [newName, setNewName] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const itemsPerPage = 10;

  const fetchData = async () => {
    setLoading(true);
    setError(null);

    try {
      const [classroomRes, cohortRes, groupRes] = await Promise.all([
        axios.get(getApiLink("classroom"), {
          params: { page: currentPage, rows: itemsPerPage, type: "classroom" },
          headers: { "X-WP-Nonce": appLocalizer.nonce },
        }),
        axios.get(getApiLink("classroom"), {
          params: { page: currentPage, rows: itemsPerPage, type: "cohort" },
          headers: { "X-WP-Nonce": appLocalizer.nonce },
        }),
        axios.get(getApiLink("classroom"), {
          params: { page: currentPage, rows: itemsPerPage, type: "group" },
          headers: { "X-WP-Nonce": appLocalizer.nonce },
        }),
      ]);

      const classroomsWithType = (classroomRes.data?.data || []).map((item) => ({
        ...item,
        type: "classroom",
      }));
      const cohortsWithType = (cohortRes.data?.data || []).map((item) => ({
        ...item,
        type: "cohort",
      }));
      const groupsWithType = (groupRes.data?.data || []).map((item) => ({
        ...item,
        type: "group",
      }));

      setClassrooms(classroomsWithType);
      setCohorts(cohortsWithType);
      setGroups(groupsWithType);
      setTotalPages(classroomRes.data?.pagination?.total_pages || 1);
    } catch (err) {
      console.error("Error fetching data:", err);
      setError(__("Failed to load data.", "moowoodle"));
    }

    setLoading(false);
  };

  useEffect(() => {
    fetchData();
  }, [currentPage]);

  const handlePageChange = (newPage) => {
    if (newPage >= 1 && newPage <= totalPages) {
      setCurrentPage(newPage);
    }
  };

  const handleViewEnroll = (group) => {
    setSelectedClassroom(group);
  };

  const handleBackToClassrooms = () => {
    setSelectedClassroom(null);
    fetchData();
  };

  const handleEditClick = (group) => {
    if (group.type === "classroom") {
      setEditingClassroom(group.classroom_id);
      setNewName(group.classroom_name || "");
    }
  };

  const handleUpdateClassroom = async (group) => {
    if (!newName.trim() || group.type !== "classroom") return;

    try {
      const response = await axios.post(
        getApiLink("classroom"),
        {
          id: group.classroom_id,
          name: newName,
          type: "classroom",
        },
        { headers: { "X-WP-Nonce": appLocalizer.nonce } }
      );

      const [success, message] = response.data;

      if (success) {
        setClassrooms((prev) =>
          prev.map((g) =>
            g.classroom_id === group.classroom_id
              ? { ...g, classroom_name: newName }
              : g
          )
        );
        setEditingClassroom(null);
        setNewName("");
      } else {
        alert(message || __("Failed to rename.", "moowoodle"));
      }
    } catch (error) {
      console.error("Error updating:", error);
      alert(__("An error occurred while renaming.", "moowoodle"));
    }
  };

  const renderEditableTitle = (group, id, name) => {
    if (group.type === "classroom" && editingClassroom === id) {
      return (
        <>
          <input
            type="text"
            value={newName}
            onChange={(e) => setNewName(e.target.value)}
            onKeyDown={(e) => e.key === "Enter" && handleUpdateClassroom(group)}
            className="edit-input"
          />
          <div className="button-group">
            <a className="cancel-btn" onClick={() => setEditingClassroom(null)}>
              {__("Cancel", "moowoodle")}
            </a>
            <a className="save-btn" onClick={() => handleUpdateClassroom(group)}>
              {__("Save", "moowoodle")}
            </a>
          </div>
        </>
      );
    }
    return (
      <div className="heading-text">
        <h2>{name}</h2>
        {group.type === "classroom" && (
          <span className="edit-button" onClick={() => handleEditClick(group)}>
            ✏️
          </span>
        )}
      </div>
    );
  };

  return (
    <div className="classroom-container">
      {selectedClassroom ? (
        <ViewEnroll
          classroom={selectedClassroom}
          onBack={handleBackToClassrooms}
        />
      ) : (
        <>
          <div className="header">
            <h1>{__("My Classroom", "moowoodle")}</h1>
          </div>

          {loading ? (
            <p>{__("Loading classrooms...", "moowoodle")}</p>
          ) : error ? (
            <p className="error-message">{error}</p>
          ) : (
            <>
              <div className="classroom-grid">
                {classrooms.map((group) => (
                  <div key={group.classroom_id} className="classroom-card">
                    <div className="classroom-title">
                      {renderEditableTitle(group, group.classroom_id, group.classroom_name)}
                    </div>
                    <ul>
                      {group.courses?.length > 0 ? (
                        group.courses.map((item, index) => (
                          <li key={index}>{item.course_name}</li>
                        ))
                      ) : (
                        <li>{__("No courses available", "moowoodle")}</li>
                      )}
                    </ul>
                    <div className="view-btn-container">
                      <button
                        className="view-button"
                        onClick={() => handleViewEnroll(group)}
                      >
                        {__("View", "moowoodle")}
                      </button>
                    </div>
                  </div>
                ))}

                {cohorts.map((cohort) => (
                  <div key={cohort.cohort_id} className="classroom-card">
                    <div className="classroom-title">
                      {renderEditableTitle(cohort, cohort.cohort_id, cohort.cohort_name)}
                    </div>
                    <div className="view-btn-container">
                      <button
                        className="view-button"
                        onClick={() => handleViewEnroll(cohort)}
                      >
                        {__("View", "moowoodle")}
                      </button>
                    </div>
                  </div>
                ))}
                {groups.map((group) => (
                  <div key={group.group_id} className="classroom-card">
                    <div className="classroom-title">
                      {renderEditableTitle(group, group.group_id, `${group.course_name} (${group.group_name})`)}
                    </div>
                    <div className="view-btn-container">
                      <button
                        className="view-button"
                        onClick={() => handleViewEnroll(group)}
                      >
                        {__("View", "moowoodle")}
                      </button>
                    </div>
                  </div>
                ))}

                {classrooms.length === 0 && cohorts.length === 0 && groups.length === 0 && (
                  <p>{__("No classrooms, cohorts, or groups found.", "moowoodle")}</p>
                )}
              </div>

              {totalPages > 1 && (
                <div className="pagination">
                  <button
                    onClick={() => handlePageChange(currentPage - 1)}
                    disabled={currentPage === 1}
                  >
                    {__("Previous", "moowoodle")}
                  </button>
                  <span className="page-info">
                    {__("Page", "moowoodle")} {currentPage} {__("of", "moowoodle")} {totalPages}
                  </span>
                  <button
                    onClick={() => handlePageChange(currentPage + 1)}
                    disabled={currentPage >= totalPages}
                  >
                    {__("Next", "moowoodle")}
                  </button>
                </div>
              )}
            </>
          )}
        </>
      )}
    </div>
  );
};

export default MyClassroom;