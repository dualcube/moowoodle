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
  const [activeTab, setActiveTab] = useState('classrooms');
  const itemsPerPage = 10;

  const fetchClassrooms = async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await axios.get(getApiLink("classroom"), {
        params: { page: currentPage, rows: itemsPerPage },
        headers: { "X-WP-Nonce": appLocalizer.nonce },
      });

      if (response.data.success) {
        setClassrooms(response.data.data.classrooms);
        setCohorts(response.data.data.cohorts);
        setGroups(response.data.data.groups);
        setTotalPages(response.data.pagination?.total_pages || 1);
      } else {
        setClassrooms([]);
        setTotalPages(1);
        setError(__("No classrooms found.", "moowoodle"));
      }
    } catch (err) {
      console.error("Error fetching classroom data:", err);
      setError(__("Failed to load classrooms.", "moowoodle"));
    }

    setLoading(false);
  };

  useEffect(() => {
    fetchClassrooms();
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
    fetchClassrooms();
  };

  const handleEditClick = (group) => {
    setEditingClassroom(group.classroom_id);
    setNewName(group.classroom_name);
  };

  const handleUpdateClassroom = async (group) => {
    if (!newName.trim()) return;

    try {
      const response = await axios.post(
        getApiLink("classroom"),
        { classroom_id: group.classroom_id, name: newName },
        { headers: { "X-WP-Nonce": appLocalizer.nonce } }
      );

      const [success, message] = response.data;

      if (success) {
        setClassrooms((prevClassrooms) =>
          prevClassrooms.map((g) =>
            g.classroom_id === group.classroom_id ? { ...g, classroom_name: newName } : g
          )
        );
        setEditingClassroom(null); // Close input field
        setNewName(""); // Clear input
      } else {
        alert(message || __("Failed to rename classroom.", "moowoodle"));
      }
    } catch (error) {
      console.error("Error renaming classroom:", error);
      alert(__("An error occurred while updating the classroom.", "moowoodle"));
    }
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
              {/* Tab Buttons */}
              <div className="tabs">
                <button
                  className={activeTab === 'classrooms' ? 'active' : ''}
                  onClick={() => setActiveTab('classrooms')}
                >
                  {__("Classrooms", "moowoodle")}
                </button>
                <button
                  className={activeTab === 'cohorts' ? 'active' : ''}
                  onClick={() => setActiveTab('cohorts')}
                >
                  {__("Cohorts", "moowoodle")}
                </button>
                <button
                  className={activeTab === 'groups' ? 'active' : ''}
                  onClick={() => setActiveTab('groups')}
                >
                  {__("Groups", "moowoodle")}
                </button>
              </div>

              {/* Tab Content */}
              {activeTab === 'classrooms' && (
                <div className="classroom-grid">
                  {classrooms.length > 0 ? (
                    classrooms.map((group) => (
                      <div key={group.classroom_id} className="classroom-card">
                        <div className="classroom-title">
                          {editingClassroom === group.classroom_id ? (
                            <>
                              <input
                                type="text"
                                value={newName}
                                onChange={(e) => setNewName(e.target.value)}
                                onKeyDown={(e) =>
                                  e.key === "Enter" && handleUpdateClassroom(group)
                                }
                                className="edit-input"
                              />
                              <div className="button-group">
                                <a
                                  className="cancel-btn"
                                  onClick={() => setEditingClassroom(null)}
                                >
                                  {__("Cancel", "moowoodle")}
                                </a>
                                <a
                                  className="save-btn"
                                  onClick={() => handleUpdateClassroom(group)}
                                >
                                  {__("Save", "moowoodle")}
                                </a>
                              </div>
                            </>
                          ) : (
                            <>
                              <div className="heading-text">
                                <h2>{group.classroom_name}</h2>
                                <span
                                  className="edit-button"
                                  onClick={() => handleEditClick(group)}
                                >
                                  ✏️
                                </span>
                              </div>
                            </>
                          )}
                        </div>

                        <ul>
                          {group.items && group.items.length > 0 ? (
                            group.items.map((item, index) => (
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
                    ))
                  ) : (
                    <p>{__("No classrooms found.", "moowoodle")}</p>
                  )}
                </div>
              )}

              {activeTab === 'cohorts' && (
                <div className="classroom-grid">
                  {cohorts.length > 0 ? (
                    cohorts.map((cohort) => (
                      <div key={cohort.cohort_id} className="classroom-card">
                        <div className="classroom-title">
                          <div className="heading-text">
                            <h2>{cohort.cohort_name}</h2>
                          </div>
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
                    ))
                  ) : (
                    <p>{__("No cohorts found.", "moowoodle")}</p>
                  )}
                </div>
              )}

              {activeTab === 'groups' && (
                <div className="classroom-grid">
                  {groups.length > 0 ? (
                    groups.map((group) => (
                      <div key={group.group_id} className="classroom-card">
                        <div className="classroom-title">
                          <div className="heading-text">
                            <h2>{group.group_name}</h2>
                          </div>
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
                    ))
                  ) : (
                    <p>{__("No groups found.", "moowoodle")}</p>
                  )}
                </div>
              )}

              {/* Pagination Controls */}
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