// import React, { useState, useEffect } from "react";
// import { __ } from "@wordpress/i18n";
// import axios from "axios";
// import { getApiLink } from "../../services/apiService";
// import ViewEnroll from "./ViewEnroll";
// import "./MyClassroom.scss";

// const MyClassroom = () => {
//   const [items, setItems] = useState([]); // Unified state for all items
//   const [currentPage, setCurrentPage] = useState(1);
//   const [totalPages, setTotalPages] = useState(1);
//   const [selectedClassroom, setSelectedClassroom] = useState(null);
//   const [editingClassroom, setEditingClassroom] = useState(null);
//   const [newName, setNewName] = useState("");
//   const [loading, setLoading] = useState(false);
//   const [error, setError] = useState(null);
//   const itemsPerPage = 5;

//   const fetchData = async () => {
//     setLoading(true);
//     setError(null);

//     try {
//       const response = await axios.get(getApiLink("classroom"), {
//         params: { page: currentPage, rows: itemsPerPage },
//         headers: { "X-WP-Nonce": appLocalizer.nonce },
//       });

//       setItems(response.data?.data || []);
//       setTotalPages(response.data?.pagination?.total_pages || 1);
//     } catch (err) {
//       console.error("Error fetching data:", err);
//       setError(__("Failed to load data.", "moowoodle"));
//     }

//     setLoading(false);
//   };

//   useEffect(() => {
//     fetchData();
//   }, [currentPage]);

//   const handlePageChange = (newPage) => {
//     if (newPage >= 1 && newPage <= totalPages) {
//       setCurrentPage(newPage);
//     }
//   };

//   const handleViewEnroll = (item) => {
//     setSelectedClassroom(item);
//   };

//   const handleBackToClassrooms = () => {
//     setSelectedClassroom(null);
//     fetchData();
//   };

//   const handleEditClick = (item) => {
//     if (item.type === "classroom") {
//       setEditingClassroom(item.classroom_id);
//       setNewName(item.classroom_name || "");
//     }
//   };

//   const handleUpdateClassroom = async (item) => {
//     if (!newName.trim() || item.type !== "classroom") return;

//     try {
//       const response = await axios.post(
//         getApiLink("classroom"),
//         {
//           id: item.classroom_id,
//           name: newName,
//           type: "classroom",
//         },
//         { headers: { "X-WP-Nonce": appLocalizer.nonce } }
//       );

//       const [success, message] = response.data;

//       if (success) {
//         setItems((prev) =>
//           prev.map((g) =>
//             g.classroom_id === item.classroom_id && g.type === "classroom"
//               ? { ...g, classroom_name: newName }
//               : g
//           )
//         );
//         setEditingClassroom(null);
//         setNewName("");
//       } else {
//         alert(message || __("Failed to rename.", "moowoodle"));
//       }
//     } catch (error) {
//       console.error("Error updating:", error);
//       alert(__("An error occurred while renaming.", "moowoodle"));
//     }
//   };

//   const renderEditableTitle = (item) => {
//     const id = item.classroom_id || item.cohort_id || item.group_id;
//     const name =
//       item.type === "classroom"
//         ? item.classroom_name
//         : item.type === "cohort"
//         ? item.cohort_name
//         : `${item.course_name} (${item.group_name})`;

//     if (item.type === "classroom" && editingClassroom === id) {
//       return (
//         <>
//           <input
//             type="text"
//             value={newName}
//             onChange={(e) => setNewName(e.target.value)}
//             onKeyDown={(e) => e.key === "Enter" && handleUpdateClassroom(item)}
//             className="edit-input"
//           />
//           <div className="button-group">
//             <a className="cancel-btn" onClick={() => setEditingClassroom(null)}>
//               {__("Cancel", "moowoodle")}
//             </a>
//             <a className="save-btn" onClick={() => handleUpdateClassroom(item)}>
//               {__("Save", "moowoodle")}
//             </a>
//           </div>
//         </>
//       );
//     }
//     return (
//       <div className="heading-text">
//         <h2>{name}</h2>
//         {item.type === "classroom" && (
//           <span className="edit-button" onClick={() => handleEditClick(item)}>
//             ✏️
//           </span>
//         )}
//       </div>
//     );
//   };

//   return (
//     <div className="classroom-container">
//       {selectedClassroom ? (
//         <ViewEnroll
//           classroom={selectedClassroom}
//           onBack={handleBackToClassrooms}
//         />
//       ) : (
//         <>
//           <div className="header">
//             <h1>{__("My Classroom", "moowoodle")}</h1>
//           </div>

//           {loading ? (
//             <p>{__("Loading items...", "moowoodle")}</p>
//           ) : error ? (
//             <p className="error-message">{error}</p>
//           ) : (
//             <>
//               <div className="classroom-grid">
//                 {items.map((item) => (
//                   <div
//                     key={`${item.type}-${item.classroom_id || item.cohort_id || item.group_id}`}
//                     className="classroom-card"
//                   >
//                     <div className="classroom-title">
//                       {renderEditableTitle(item)}
//                     </div>
//                     {item.type === "classroom" && (
//                       <ul>
//                         {item.courses?.length > 0 ? (
//                           item.courses.map((course, index) => (
//                             <li key={index}>{course.course_name}</li>
//                           ))
//                         ) : (
//                           <li>{__("No courses available", "moowoodle")}</li>
//                         )}
//                       </ul>
//                     )}
//                     <div className="view-btn-container">
//                       <button
//                         className="view-button"
//                         onClick={() => handleViewEnroll(item)}
//                       >
//                         {__("View", "moowoodle")}
//                       </button>
//                     </div>
//                   </div>
//                 ))}

//                 {items.length === 0 && (
//                   <p>{__("No classrooms, cohorts, or groups found.", "moowoodle")}</p>
//                 )}
//               </div>

//               {totalPages > 1 && (
//                 <div className="pagination">
//                   <button
//                     onClick={() => handlePageChange(currentPage - 1)}
//                     disabled={currentPage === 1}
//                   >
//                     {__("Previous", "moowoodle")}
//                   </button>
//                   <span className="page-info">
//                     {__("Page", "moowoodle")} {currentPage} {__("of", "moowoodle")}{" "}
//                     {totalPages}
//                   </span>
//                   <button
//                     onClick={() => handlePageChange(currentPage + 1)}
//                     disabled={currentPage >= totalPages}
//                   >
//                     {__("Next", "moowoodle")}
//                   </button>
//                 </div>
//               )}
//             </>
//           )}
//         </>
//       )}
//     </div>
//   );
// };

// export default MyClassroom;

import React, { useState, useEffect, useCallback } from "react";
import { __ } from "@wordpress/i18n";
import axios from "axios";
import { getApiLink } from "../../services/apiService";
import ViewEnroll from "./ViewEnroll";
import "./MyClassroom.scss";

const MyClassroom = () => {
  const [items, setItems] = useState([]);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [selectedItem, setSelectedItem] = useState(null);
  const [editingId, setEditingId] = useState(null);
  const [newName, setNewName] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const itemsPerPage = 4;

  // Memoized fetchData function
  const fetchData = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await axios.get(getApiLink("classroom"), {
        params: { page: currentPage, rows: itemsPerPage },
        headers: { "X-WP-Nonce": appLocalizer.nonce },
      });

      setItems(response.data?.data || []);
      setTotalPages(response.data?.pagination?.total_pages || 1);
    } catch (err) {
      console.error("Error fetching data:", err);
      setError(__("Failed to load data. Please try again.", "moowoodle"));
    } finally {
      setLoading(false);
    }
  }, [currentPage]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  const handlePageChange = (newPage) => {
    if (newPage >= 1 && newPage <= totalPages) {
      setCurrentPage(newPage);
    }
  };

  const handleViewEnroll = (item) => {
    setSelectedItem(item);
  };

  const handleBack = () => {
    setSelectedItem(null);
    fetchData();
  };

  const handleEditClick = (item) => {
    if (item.type === "classroom") {
      setEditingId(item.classroom_id);
      setNewName(item.classroom_name || "");
    }
  };

  const handleUpdateName = async (item) => {
    if (!newName.trim() || item.type !== "classroom") return;

    setLoading(true);
    try {
      const response = await axios.post(
        getApiLink("classroom"),
        {
          id: item.classroom_id,
          name: newName,
          type: "classroom",
        },
        { headers: { "X-WP-Nonce": appLocalizer.nonce } }
      );

      const [success, message] = response.data;

      if (success) {
        setItems((prev) =>
          prev.map((g) =>
            g.classroom_id === item.classroom_id && g.type === "classroom"
              ? { ...g, classroom_name: newName }
              : g
          )
        );
        setEditingId(null);
        setNewName("");
      } else {
        alert(message || __("Failed to rename.", "moowoodle"));
      }
    } catch (error) {
      console.error("Error updating:", error);
      alert(__("An error occurred while renaming.", "moowoodle"));
    } finally {
      setLoading(false);
    }
  };

  const getItemName = (item) => {
    switch (item.type) {
      case "classroom":
        return item.classroom_name;
      case "cohort":
        return item.cohort_name;
      case "group":
        return `${item.course_name} (${item.group_name})`;
      default:
        return "";
    }
  };

  const renderEditableTitle = (item) => {
    const id = item.classroom_id || item.cohort_id || item.group_id;
    const name = getItemName(item);

    if (item.type === "classroom" && editingId === id) {
      return (
        <div className="edit-container">
          <input
            type="text"
            value={newName}
            onChange={(e) => setNewName(e.target.value)}
            onKeyDown={(e) => e.key === "Enter" && handleUpdateName(item)}
            className="edit-input"
            placeholder={__("Enter new name", "moowoodle")}
            aria-label={__("Edit classroom name", "moowoodle")}
            disabled={loading}
          />
          <div className="button-group">
            <button
              className="cancel-btn"
              onClick={() => setEditingId(null)}
              disabled={loading}
              aria-label={__("Cancel edit", "moowoodle")}
            >
              {__("Cancel", "moowoodle")}
            </button>
            <button
              className="save-btn"
              onClick={() => handleUpdateName(item)}
              disabled={loading || !newName.trim()}
              aria-label={__("Save new name", "moowoodle")}
            >
              {loading ? __("Saving...", "moowoodle") : __("Save", "moowoodle")}
            </button>
          </div>
        </div>
      );
    }

    return (
      <div className="heading-text">
        <h2>{name}</h2>
        {item.type === "classroom" && (
          <button
            className="edit-button"
            onClick={() => handleEditClick(item)}
            aria-label={__("Edit classroom name", "moowoodle")}
          >
            ✏️
          </button>
        )}
      </div>
    );
  };

  return (
    <div className="classroom-container">
      {selectedItem ? (
        <ViewEnroll classroom={selectedItem} onBack={handleBack} />
      ) : (
        <>
          <header className="classroom-header">
            <h1>{__("My Classroom", "moowoodle")}</h1>
          </header>

          {loading && !items.length ? (
            <div className="loading-spinner" aria-live="polite">
              {__("Loading items...", "moowoodle")}
            </div>
          ) : error ? (
            <div className="error-message" role="alert">
              {error}
              <button
                className="retry-btn"
                onClick={fetchData}
                aria-label={__("Retry loading data", "moowoodle")}
              >
                {__("Retry", "moowoodle")}
              </button>
            </div>
          ) : (
            <>
              <div className="classroom-grid">
                {items.length ? (
                  items.map((item) => (
                    <div
                      key={`${item.type}-${item.classroom_id || item.cohort_id || item.group_id}`}
                      className="classroom-card"
                    >
                      <div className="classroom-title">
                        {renderEditableTitle(item)}
                      </div>
                      {item.type === "classroom" && (
                        <ul className="course-list">
                          {item.courses?.length ? (
                            item.courses.map((course) => (
                              <li key={course.item_id}>{course.course_name}</li>
                            ))
                          ) : (
                            <li className="no-courses">
                              {__("No courses available", "moowoodle")}
                            </li>
                          )}
                        </ul>
                      )}
                      <div className="view-btn-container">
                        <button
                          className="view-button"
                          onClick={() => handleViewEnroll(item)}
                          aria-label={__("View details for", "moowoodle") + " " + getItemName(item)}
                        >
                          {__("View", "moowoodle")}
                        </button>
                      </div>
                    </div>
                  ))
                ) : (
                  <p className="no-items">
                    {__("No classrooms, cohorts, or groups found.", "moowoodle")}
                  </p>
                )}
              </div>

              {totalPages > 1 && (
                <nav className="pagination" aria-label={__("Pagination", "moowoodle")}>
                  <button
                    onClick={() => handlePageChange(currentPage - 1)}
                    disabled={currentPage === 1}
                    aria-label={__("Previous page", "moowoodle")}
                  >
                    {__("Previous", "moowoodle")}
                  </button>
                  <span className="page-info" aria-live="polite">
                    {__("Page", "moowoodle")} {currentPage} {__("of", "moowoodle")} {totalPages}
                  </span>
                  <button
                    onClick={() => handlePageChange(currentPage + 1)}
                    disabled={currentPage >= totalPages}
                    aria-label={__("Next page", "moowoodle")}
                  >
                    {__("Next", "moowoodle")}
                  </button>
                </nav>
              )}
            </>
          )}
        </>
      )}
    </div>
  );
};

export default MyClassroom;