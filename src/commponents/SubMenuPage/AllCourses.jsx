import React, { useState, useEffect } from "react";
import axios from "axios";
import Tabs from "./../Common/Tabs";
import ProOverlay from "./../Common/ProOverlay";
import logo from "../../../assets/images/logo-moowoodle-pro.png";

const LoadingSpinner = () => (
  <tr>
    <td
      colSpan={MooWoodleAppLocalizer.from_heading.length}
      style={{ textAlign: "center" }}
    >
      <div className="loading-spinner">
        <img className="lodaer-img-overlay" src={logo} alt="" />
      </div>
    </td>
  </tr>
);

const AllCourses = () => {
  const [courses, setCourses] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Fetch data from the WordPress REST API
    const fetchData = async () => {
      try {
        const response = await axios.get(
          `${MooWoodleAppLocalizer.rest_url}moowoodle/v1/fetch-all-courses`,
          {
            headers: { "X-WP-Nonce": MooWoodleAppLocalizer.nonce },
          }
        );
        setCourses(response.data);
        setLoading(false);
      } catch (error) {
        console.error("Error fetching data:", error);
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  return (
    <>
      <div class="mw-middle-child-container">
        <Tabs />
        <div class="mw-tab-content">
          <div class="mw-dynamic-fields-wrapper">
            <form class="mw-dynamic-form" action="options.php" method="post">
              <div id="moowoodle-link-course-table" class="mw-section-wraper">
                <div class="mw-section-child-wraper">
                  <div class="mw-header-search-wrap">
                    <div class="mw-section-header">
                      <h3>Courses</h3>
                    </div>
                  </div>
                  <div class="mw-section-containt">
                    <div class="mw-form-group">
                      <div className="mw-input-content">
                        <div className="mw-course-table-content ">
                          <div className="moowoodle-table-fuilter"></div>
                          <div className="search-bulk-action">
                            <div
                              className={`${MooWoodleAppLocalizer.pro_popup_overlay} mw-filter-bulk`}
                            >
                              <label
                                htmlFor="bulk-action-selector-top"
                                className="screen-reader-text"
                              >
                                {MooWoodleAppLocalizer.bulk_actions_label}
                              </label>
                              <select
                                name="action"
                                id="bulk-action-selector-top"
                              >
                                <option value="-1">
                                  {MooWoodleAppLocalizer.bulk_actions}
                                </option>
                                <option value="sync_courses">
                                  {MooWoodleAppLocalizer.sync_course}
                                </option>
                                <option value="sync_create_product">
                                  {MooWoodleAppLocalizer.create_product}
                                </option>
                                <option value="sync_update_product">
                                  {MooWoodleAppLocalizer.update_product}
                                </option>
                              </select>
                              <button
                                className={`button-secondary bulk-action-select-apply ${MooWoodleAppLocalizer.pro_popup_overlay}`}
                                name="bulk-action-apply"
                                type="button"
                              >
                                {MooWoodleAppLocalizer.apply}
                              </button>
                              <div
                                dangerouslySetInnerHTML={{
                                  __html: MooWoodleAppLocalizer.pro_sticker,
                                }}
                              ></div>
                            </div>
                          </div>
                        </div>
                        <table
                          id="moowoodle_table"
                          className="table table-bordered responsive-table moodle-linked-courses widefat"
                        >
                          <thead>
                            <tr>
                              {MooWoodleAppLocalizer.from_heading.map(
                                (heading, index) => (
                                  <th
                                    key={index}
                                    dangerouslySetInnerHTML={{
                                      __html: heading,
                                    }}
                                  ></th>
                                )
                              )}
                            </tr>
                          </thead>
                          <tbody>
                            {loading ? (
                              <LoadingSpinner />
                            ) : (
                              courses.map((course) => (
                                <tr key={course.id}>
                                  <td>
                                    <input
                                      type="checkbox"
                                      className="bulk-action-checkbox"
                                      name="bulk_action_seclect_course_id[]"
                                      value={course.ID}
                                    />
                                  </td>
                                  <td
                                    dangerouslySetInnerHTML={{
                                      __html: course.moodle_url,
                                    }}
                                  ></td>
                                  <td
                                    dangerouslySetInnerHTML={{
                                      __html: course.course_short_name,
                                    }}
                                  ></td>
                                  <td
                                    dangerouslySetInnerHTML={{
                                      __html: course.product_name,
                                    }}
                                  ></td>
                                  <td
                                    dangerouslySetInnerHTML={{
                                      __html: course.catagory_name,
                                    }}
                                  ></td>
                                  <td
                                    dangerouslySetInnerHTML={{
                                      __html: course.enroled_user,
                                    }}
                                  ></td>
                                  <td
                                    dangerouslySetInnerHTML={{
                                      __html: course.date,
                                    }}
                                  ></td>
                                  <td
                                    dangerouslySetInnerHTML={{
                                      __html: course.actions,
                                    }}
                                  ></td>
                                </tr>
                              ))
                            )}
                          </tbody>
                        </table>
                        <br />
                        <p className="mw-sync-paragraph">
                          {MooWoodleAppLocalizer.cannot_find_course}
                          <a
                            href={`${MooWoodleAppLocalizer.admin_url}admin.php?page=moowoodle#&tab=moowoodle-synchronization&sub-tab=moowoodle-sync-now`}
                          >
                            {MooWoodleAppLocalizer.sync_moodle_courses}
                          </a>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                {MooWoodleAppLocalizer.porAdv && <ProOverlay />}
              </div>
            </form>
          </div>
        </div>
      </div>
    </>
  );
};
export default AllCourses;
