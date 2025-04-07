import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import axios from "axios";
import { getApiLink } from "../../services/apiService";
import ViewEnroll from "./ViewEnroll";
import "./MyClassroom.scss";

const MyClassroom = () => {
    const [classrooms, setClassrooms] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [selectedClassroom, setSelectedClassroom] = useState(null);
    const [editingClassroom, setEditingClassroom] = useState(null);
    const [newName, setNewName] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const itemsPerPage = 10;

    const fetchClassrooms = async () => {
        setLoading(true);
        setError(null);
    
        try {
            const response = await axios.get(
                getApiLink("classroom"),
                {
                    params: { page: currentPage, rows: itemsPerPage },
                    headers: { "X-WP-Nonce": appLocalizer.nonce }
                }
            );
    
            if (response.data.success && Array.isArray(response.data.data)) {
                setClassrooms(response.data.data);
                setTotalPages(response.data.pagination?.total_pages || 1);
            } else {
                setClassrooms([]);
                setTotalPages(1);
                setError(__("No classrooms found.", "moowoodle-pro"));
            }
        } catch (err) {
            console.error("Error fetching classroom data:", err);
            setError(__("Failed to load classrooms.", "moowoodle-pro"));
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
        setEditingClassroom(group.group_id);
        setNewName(group.group_name);
    };

    const handleUpdateClassroom = async (group) => {
        if (!newName.trim()) return;
    
        try {
            const response = await axios.post(
                getApiLink("classroom"),
                { group_id: group.group_id, name: newName },
                { headers: { "X-WP-Nonce": appLocalizer.nonce } }
            );
    
            const [success, message] = response.data;
    
            if (success) {
                setClassrooms((prevClassrooms) =>
                    prevClassrooms.map((g) =>
                        g.group_id === group.group_id ? { ...g, group_name: newName } : g
                    )
                );
                setEditingClassroom(null); // Close input field
                setNewName(""); // Clear input
            } else {
                alert(message || __("Failed to rename classroom.", "moowoodle-pro"));
            }
        } catch (error) {
            console.error("Error renaming classroom:", error);
            alert(__("An error occurred while updating the classroom.", "moowoodle-pro"));
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
                        <h1>{__("My Classroom", "moowoodle-pro")}</h1>
                    </div>

                    {loading ? (
                        <p>{__("Loading classrooms...", "moowoodle-pro")}</p>
                    ) : error ? (
                        <p className="error-message">{error}</p>
                    ) : (
                        <>
                            <div className="classroom-grid">
                                {classrooms.length > 0 ? (
                                    classrooms.map((group) => (
                                        <div key={group.group_id} className="classroom-card">
                                            <div className="classroom-title">
                                                {editingClassroom === group.group_id ? (
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
                                                                {__("Cancel", "moowoodle-pro")}
                                                            </a>
                                                            <a className="save-btn" onClick={() => handleUpdateClassroom(group)}>
                                                                {__("Save", "moowoodle-pro")}
                                                            </a>
                                                        </div>
                                                    </>
                                                ) : (
                                                    <>
                                                        <div className="heading-text">
                                                            <h2>{group.group_name}</h2>
                                                            <span className="edit-button" onClick={() => handleEditClick(group)}>
                                                                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.1213 2.70705C19.9497 1.53548 18.0503 1.53547 16.8787 2.70705L15.1989 4.38685L7.29289 12.2928C7.16473 12.421 7.07382 12.5816 7.02986 12.7574L6.02986 16.7574C5.94466 17.0982 6.04451 17.4587 6.29289 17.707C6.54127 17.9554 6.90176 18.0553 7.24254 17.9701L11.2425 16.9701C11.4184 16.9261 11.5789 16.8352 11.7071 16.707L19.5556 8.85857L21.2929 7.12126C22.4645 5.94969 22.4645 4.05019 21.2929 2.87862L21.1213 2.70705ZM18.2929 4.12126C18.6834 3.73074 19.3166 3.73074 19.7071 4.12126L19.8787 4.29283C20.2692 4.68336 20.2692 5.31653 19.8787 5.70705L18.8622 6.72357L17.3068 5.10738L18.2929 4.12126ZM15.8923 6.52185L17.4477 8.13804L10.4888 15.097L8.37437 15.6256L8.90296 13.5112L15.8923 6.52185ZM4 7.99994C4 7.44766 4.44772 6.99994 5 6.99994H10C10.5523 6.99994 11 6.55223 11 5.99994C11 5.44766 10.5523 4.99994 10 4.99994H5C3.34315 4.99994 2 6.34309 2 7.99994V18.9999C2 20.6568 3.34315 21.9999 5 21.9999H16C17.6569 21.9999 19 20.6568 19 18.9999V13.9999C19 13.4477 18.5523 12.9999 18 12.9999C17.4477 12.9999 17 13.4477 17 13.9999V18.9999C17 19.5522 16.5523 19.9999 16 19.9999H5C4.44772 19.9999 4 19.5522 4 18.9999V7.99994Z" fill="#000000"/>
                                                                </svg>
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
                                                    <li>{__("No courses available", "moowoodle-pro")}</li>
                                                )}
                                            </ul>
                                            <div className="view-btn-container">
                                                <button className="view-button" onClick={() => handleViewEnroll(group)}>
                                                    {__("View", "moowoodle-pro")}
                                                </button>
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <p>{__("No classrooms found.", "moowoodle-pro")}</p>
                                )}
                            </div>

                            {/* Pagination Controls */}
                            {totalPages > 1 && (
                                <div className="pagination">
                                    <button 
                                        onClick={() => handlePageChange(currentPage - 1)} 
                                        disabled={currentPage === 1}
                                    >
                                        {__("Previous", "moowoodle-pro")}
                                    </button>

                                    <span className="page-info">
                                        {__("Page", "moowoodle-pro")} {currentPage} {__("of", "moowoodle-pro")} {totalPages}
                                    </span>

                                    <button 
                                        onClick={() => handlePageChange(currentPage + 1)} 
                                        disabled={currentPage >= totalPages}
                                    >
                                        {__("Next", "moowoodle-pro")}
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
