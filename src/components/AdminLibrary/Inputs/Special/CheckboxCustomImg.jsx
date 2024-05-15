import React from "react";
import WordPress from "../../../../assets/images/WordPress.png";
import Moodle from "../../../../assets/images/Moodle.png";
import './CheckboxCustomImg.scss';

const CheckboxCustomImg = () => {
  return (
    <>
      <div className="custom-sync-section">
        <div className="sync-direction-items">
          <div className="input-section">
            <input type="checkbox" />
          </div>
          <div className="sync-meta-wrapper">
            <div className="img-section">
              <img src={WordPress} alt="" />
              <div className="connection-arrow">
               <i className="admin-font font-arrow-right"></i>
              </div>
              <img src={Moodle} alt="" />
            </div>
            <p className="sync-label">WordPress to Mooble</p>
          </div>
        </div>
        <div className="sync-direction-items">
          <div className="input-section">
            <input type="checkbox" />
          </div>
          <div className="sync-meta-wrapper">
            <div className="img-section">
              <img src={Moodle} alt="" />
              <div className="connection-arrow">
               <i className="admin-font font-arrow-right"></i>
              </div>
              <img src={WordPress} alt="" />
            </div>
            <p className="sync-label">Mooble to WordPress</p>
          </div>
        </div>
      </div>
    </>
  );
};

export default CheckboxCustomImg;
