import { Link } from "react-router-dom";
import Brand from "../../../assets/images/Brand.png";
import BrandSmall from "../../../assets/images/Brand-small.png";
import "./tabs.scss";
import { useState } from "react";
import AdminFooter from "../../AdminFooter/AdminFooter";

const Tabs = (props) => {
  const {
    tabData,
    currentTab,
    getForm,
    prepareUrl,
    HeaderSection,
    BannerSection,
  } = props;

  const [menuCol, setMenuCol] = useState(false);
  const [openedSubtab, setOpenedSubtab] = useState("");

  const showTabSection = (tab) => {
    return tab.link ? (
      <a href={tab.link}>
        <div>{tab.icon && <i className={`admin-font ${tab.icon}`}></i>}</div>
        <div>
          <p className="menu-name">{menuCol ? null : tab.name}</p>
          <p className="menu-desc">{menuCol ? null : tab.desc}</p>
        </div>
      </a>
    ) : (
      <Link
        className={currentTab === tab.id ? "active-current-tab" : ""}
        to={prepareUrl(tab.id)}
      >
        <div>
          {tab.icon && <i className={` admin-font ${tab.icon} `}></i>}
          {menuCol
            ? null
            : !appLocalizer.pro_active &&
              tab.proDependent && <span class="admin-pro-tag">Pro</span>}
        </div>
        <div>
          <p className="menu-name">{menuCol ? null : tab.name}</p>
          <p className="menu-desc">{menuCol ? null : tab.desc}</p>
        </div>
      </Link>
    );
  };

  const showHideMenu = (tab) => {
    return (
      <Link
        className={currentTab === tab.id ? "active-current-tab" : ""}
        onClick={(e) => {
          e.preventDefault();
          if (openedSubtab == tab.id) {
            setOpenedSubtab("");
          } else {
            setOpenedSubtab(tab.id);
          }
        }}
      >
        <div>{tab.icon && <i className={` admin-font ${tab.icon} `}></i>}</div>
        <div className="drop-down-section">
          <div>
            <p className="menu-name">{menuCol ? null : tab.name}</p>
            <p className="menu-desc">{menuCol ? null : tab.desc}</p>
          </div>
          {menuCol ? null : openedSubtab == tab.id ? (
            <p className="tab-menu-dropdown-icon active">
              <i className="admin-font font-keyboard_arrow_down"></i>
            </p>
          ) : (
            <p className="tab-menu-dropdown-icon">
              <i className="admin-font font-keyboard_arrow_down"></i>
            </p>
          )}
        </div>
      </Link>
    );
  };

  // Get the description of the current tab.
  const getTabDescription = (tabData) => {
    return tabData.map(({ content, type }) => {
      if (type === "file") {
        return (
          content.id === currentTab &&
          content.id !== "support" && (
            <div className="tab-description-start">
              <div className="child">
                <p><i className={`admin-font ${content.icon}`}></i></p>
                <div>
                  <div className="tab-name">{content.name}</div>
                  <div className="tab-desc">{content.desc}</div>
                </div>
              </div>
            </div>
          )
        );
      } else if (type === "folder") {
        // Get tab description from child by recursion
        return getTabDescription(content);
      }
    });
  };

  const handleMenuShow = () => {
    setMenuCol(!menuCol);
  };

  return (
    <>
      <div className={` general-wrapper ${props.queryName} `}>
        {HeaderSection && <HeaderSection />}

        {BannerSection && <BannerSection />}
        <div
          className={`middle-container-wrapper ${
            props.horizontally ? "horizontal-tabs" : "vertical-tabs"
          }`}
        >
          <div
            className={`${menuCol ? "showMenu" : ""} middle-child-container`}
          >
            <div id="current-tab-lists" className="current-tab-lists">
              <div className="brand">
                <img className="logo" src={menuCol ? BrandSmall : Brand} alt="Logo" />
                <img className="logo-small" src={BrandSmall} alt="Logo" />
                {menuCol ? null : <p>{appLocalizer.tab_name}</p>}
              </div>
              <div className="current-tab-lists-container">
                {tabData.map(({ type, content }) => {
                  if (type !== "folder") {
                    return showTabSection(content);
                  }

                  // Tab has child tabs
                  return (
                    <div className="tab-wrapper">
                      {showHideMenu(content[0].content)}
                      {
                        <div
                          className={`subtab-wrapper ${menuCol && "show"} ${
                            openedSubtab == content[0].content.id && "active"
                          }`}
                        >
                          {content.slice(1).map(({ type, content }) => {
                            return showTabSection(content);
                          })}
                        </div>
                      }
                    </div>
                  );
                })}
                <button className="menu-coll-btn" onClick={handleMenuShow}>
                  <span>
                    <i className="admin-font font-arrow-left"></i>
                  </span>
                  {menuCol ? null : "Collapse"}
                </button>
              </div>
            </div>
            <div className="tab-content">
              {/* Render name and description of the current tab */}
              {getTabDescription(tabData)}
              {/* Render the form from parent component for better control */}
              {getForm(currentTab)}
            </div>
          </div>
        </div>

        <AdminFooter/>
      </div>
    </>
  );
};

export default Tabs;
