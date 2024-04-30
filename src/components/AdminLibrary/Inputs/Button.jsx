import React from "react";

export default function Button(props) {
  return (
    <div className={props.wrapperClass}>
      <input
        className={props.inputClass}
        type={props.type}
        value={props.value}
        onClick={(e) => {
          props.onClick(e);
        }}
      />
      {props.proSetting && <span className="admin-pro-tag">pro</span>}
      {props.description && (
        <p
          className={props.descClass}
          dangerouslySetInnerHTML={{ __html: props.description }}
        ></p>
      )}
    </div>
  );
}
