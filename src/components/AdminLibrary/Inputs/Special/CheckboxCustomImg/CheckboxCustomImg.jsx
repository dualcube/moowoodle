import React from "react";
// import WordPress from "../../assets/images/WordPress.png";
// import Moodle from "../../assets/images/Moodle.png";
import './CheckboxCustomImg.scss';

const CheckboxCustomImg = (props) => {
  let value = props.value || [];

  return (
    <>
      <div className="custom-sync-section">
        <div className="sync-direction-items">
          <input
            checked={ props.value.includes( 'wordpress_to_moodle' ) }
            onClick={(e) => {
              value = value.filter(element => element !== 'wordpress_to_moodle' );
              if ( e.target.checked ){
                value.push( 'wordpress_to_moodle' );
              } 
              props.onChange( value )
            }}
            type="checkbox"
          />
            <div className="sync-meta-wrapper">
              <img src={props.image1} alt="" />
              <i className="admin-font font-arrow-right"></i>
              <img src={props.image2} alt="" />
          </div>
          <p className="sync-label">WordPress to Moodle</p>
        </div>
        <div className="sync-direction-items">
          <input
            checked={ props.value.includes( 'moodle_to_wordpress' ) }
            onClick={(e) => {
              value = value.filter(element => element !== 'moodle_to_wordpress' );
              if ( e.target.checked ){
                value.push( 'moodle_to_wordpress' );
              } 
              props.onChange( value )
            }}
            type="checkbox"
          />
            <div className="sync-meta-wrapper">
              <img src={props.image2} alt="" />
              <i className="admin-font font-arrow-right"></i>
              <img src={props.image1} alt="" />
          </div>
          <p className="sync-label">Moodle to WordPress</p>
        </div>
        {props.proSetting && <span className="admin-pro-tag">pro</span>}
      </div>
      {props.description && (<p className="settings-metabox-description" dangerouslySetInnerHTML= {{__html: props.description}}></p>)}
    </>
  );
};

export default CheckboxCustomImg;
