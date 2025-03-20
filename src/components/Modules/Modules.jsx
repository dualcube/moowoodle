import { useState, useEffect } from "react";
import Dialog from "@mui/material/Dialog";
import Popoup from "../PopupContent/PopupContent";

import { getApiLink, sendApiResponse } from "../../services/apiService";
import { useModules } from "../../contexts/ModuleContext";
// import context
import { getModuleData } from "../../services/templateService";
import "./modules.scss";

const Modules = () => {
  const { modules, insertModule, removeModule } = useModules();
  const modulesArray = getModuleData();
  const [modelOpen, setModelOpen] = useState(false);
  const [successMsg, setSuccessMsg] = useState("");

  /**
   * Check whether a module is active or not.
   * @param {*} moduleId 
   * @returns 
   */
  const isModuleAvailable = ( moduleId ) => {
    const module = modulesArray.find((module) => module.id === moduleId);

    if ( ! module?.pro_module ) return true;
    if ( module?.pro_module && appLocalizer.khali_dabba ) return true;
    return false;
  }

  /**
   * Handle module activation and deactivation.
   * @param {*} event 
   * @param {*} moduleId 
   * @returns 
   */
  const handleOnChange = async (event, moduleId) => {
    if ( ! isModuleAvailable( moduleId ) ) {
      setModelOpen(true);
      return;
    }

    const action = event.target.checked ? "activate" : "deactivate";
    if (action == "activate") {
      insertModule(moduleId);
    } else {
      removeModule(moduleId);
    }
    
    const response = await sendApiResponse(getApiLink("modules"), {
      id: moduleId,
      action,
    });

    setSuccessMsg('Module activated');
    setTimeout(() => setSuccessMsg(''), 2000);
  };

  return (
    <>
      <div className="module-container">
        <Dialog
          className="admin-module-popup"
          open={modelOpen}
          onClose={() => setModelOpen(false) }
          aria-labelledby="form-dialog-title"
        >
          <span
            className="admin-font adminLib-cross"
            onClick={() => setModelOpen(false) }
          ></span>
          <Popoup />
        </Dialog>

        {successMsg && (
          <div className="admin-notice-display-title">
            <i className="admin-font adminLib-icon-yes"></i>
            {successMsg}
          </div>
        )}
        
        <div className="tab-name">
          <h1>Modules</h1>
        </div>
        <div className="module-option-row">
          {modulesArray.map((module) => (
            <div className="module-list-item">
              {module.pro_module && !appLocalizer.khali_dabba && <span className="admin-pro-tag">Pro</span>}
              <div className="module-icon">
                <i className={`font ${module.icon}`}></i>
              </div>

              <div className="card-meta">
                <div className="meta-name">{module.name}</div>
                <p className="meta-description" dangerouslySetInnerHTML={{ __html: module.desc }}></p>
              </div>
              <div className="card-footer">
                <div className="card-support">
                  <a href={module.doc_link} className="main-btn btn-purple card-support-btn">Docs</a>
                  <a href={module.settings_link} className="main-btn btn-purple card-support-btn">Setting</a>
                </div>
                <div className="toggle-checkbox-content" data={`${module.id}-showcase-tour`}>
                  <input
                    type="checkbox"
                    className="woo-toggle-checkbox"
                    id={`toggle-switch-${module.id}`}
                    // checked={modules.includes(module.id)}
                    onChange={(e) => handleOnChange(e, module.id)} 
                  />
                  <label htmlFor={`toggle-switch-${module.id}`} className="toggle-switch-is_hide_cart_checkout"></label>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </>
  );
};

export default Modules;
