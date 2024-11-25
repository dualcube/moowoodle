import React from "react";
import './CheckboxCustomImg.scss';

const CheckboxCustomImg = (props) => {
  const {value = [], onChange, syncDirections, description, proSetting} = props;

  const handleCheckboxChange = (directionValue, isChecked) => {
    let updatedValue = [...value];
    updatedValue = updatedValue.filter((element) => element !== directionValue);

    if (isChecked) {
      updatedValue.push(directionValue);
    }

    onChange(updatedValue);
  };

  return (
    <>
      <div className="custom-sync-section">
        {syncDirections.map((direction, index) => (
          <div className="sync-direction-items" key={index}>
            <input
              type="checkbox"
              checked={value.includes(direction.value)}
              onChange={(e) => handleCheckboxChange(direction.value, e.target.checked)}
            />
            <div className="sync-meta-wrapper">
              <img src={direction.img1} alt="" />
              <i className="admin-font adminLib-arrow-right"></i>
              <img src={direction.img2} alt="" />
            </div>
            <p className="sync-label">{direction.label}</p>
          </div>
        ))}
        
        {/* Render the pro tag if needed */}
        {proSetting && <span className="admin-pro-tag">pro</span>}
      </div>

      {/* Render the description if provided */}
      {description && (
        <p className="settings-metabox-description" dangerouslySetInnerHTML={{ __html: description }}></p>
      )}
    </>
  );
};

export default CheckboxCustomImg;
