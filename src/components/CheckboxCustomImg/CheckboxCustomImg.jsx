import React from "react";
import WordPress from "../../assets/images/WordPress.png";
import Moodle from "../../assets/images/Moodle.png";
import './CheckboxCustomImg.scss';

const CheckboxCustomImg = () => {
  return (
    <>
      <div className="custom-sync-section">
        <div className="sync-direction-items">
          <input type="checkbox" />
            <div className="sync-meta-wrapper">
              <img src={WordPress} alt="" />
              <i className="admin-font font-arrow-right"></i>
              <img src={Moodle} alt="" />
          </div>
          <p className="sync-label">WordPress to Moodle</p>
        </div>
        <div className="sync-direction-items">
          <input type="checkbox" />
            <div className="sync-meta-wrapper">
              <img src={Moodle} alt="" />
              <i className="admin-font font-arrow-right"></i>
              <img src={WordPress} alt="" />
          </div>
          <p className="sync-label">Moodle to WordPress</p>
        </div>
      </div>
    </>
  );
};

export default CheckboxCustomImg;
