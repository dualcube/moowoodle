import { useState, useEffect, useRef } from "react";
import "./dynamicForm.scss";
import CustomInput from "../Inputs";

// import context.
import { useSetting } from "../../../contexts/SettingContext";

// import services function
import { getApiLink, sendApiResponse } from "../../../services/apiService";
import Dialog from "@mui/material/Dialog";
import Popoup from "../../PopupContent/PopupContent";
import FormCustomizer from "../Inputs/Special/FormCustomizer";
import ConnectButton from "../../ConnectButton/ConnectButton";
import Log from "../../Log/Log";
import SSOKey from "../../SSOKey/SSOKey";
import SyncNow from "../../SyncNow/SyncNow";
import SyncMap from "../../SyncMap/SyncMap";
import ScheduleInterval from "../../ScheduleInterval/ScheduleInterval";
import CheckboxCustomImg from "../../CheckboxCustomImg/CheckboxCustomImg";

// Variable for controll coldown effect submit time
const PENALTY = 10;
const COOLDOWN = 1;

const DynamicForm = (props) => {
  const { modal, submitUrl, id } = props.setting;
  const { setting, updateSetting } = useSetting();
  const [successMsg, setSuccessMsg] = useState("");
  const [countryState, setCountryState] = useState([]);
  const settingChanged = useRef(false);
  const [modelOpen, setModelOpen] = useState(false);

  const counter = useRef(0);
  const counterId = useRef(0);

  // Submit the setting to backend when setting Change.
  useEffect(() => {
    if (settingChanged.current) {
      settingChanged.current = false;

      // Set counter by penalti
      counter.current = PENALTY;
      // Clear previous counter.
      if (counterId.current) {
        clearInterval(counterId.current);
      }

      // Create new interval
      const intervalId = setInterval(() => {
        counter.current -= COOLDOWN;
        // Cooldown compleate time for db request.
        if (counter.current < 0) {
          sendApiResponse(getApiLink(submitUrl), {
            setting: setting,
            settingName: id,
            vendor_id: props.vendorId || "",
            announcement_id: props.announcementId || "",
            knowladgebase_id: props.knowladgebaseId || "",
          }).then((response) => {
            // Set success messaage for 2second.
            setSuccessMsg(response);
            setTimeout(() => setSuccessMsg(""), 2000);

            // If response has redirect link then redirect.
            if (response.redirect_link) {
              window.location.href = response.data.redirect_link;
            }
          });

          clearInterval(intervalId);
          counterId.current = 0;
        }
      }, 50);

      // Store the interval id.
      counterId.current = intervalId;
    }
  }, [setting]);

  const isProSetting = (proDependent) => {
    return proDependent && !appLocalizer.pro_active;
  }

  const proSettingChanged = (isProSetting) => {
    if (isProSetting && !appLocalizer.pro_active) {
      setModelOpen(true);
      return true;
    }
    return false;
  }


  const handleChange = (event, key, type = 'single', fromType = 'simple', arrayValue = []) => {
    settingChanged.current = true;

    if (type === 'single') {
      if (fromType === 'simple') {
        updateSetting(key, event.target.value);
      } else if (fromType === 'calender') {
        updateSetting(key, event.join(','));
      } else if (fromType === 'select') {
        updateSetting(key, arrayValue[event.index]);
      } else if (fromType === 'multi-select') {
        updateSetting(key, event);
      } else if (fromType === 'wpeditor') {
        updateSetting(key, event);
      } else if (fromType === 'country') {
        updateSetting(key, arrayValue[event.index]);
        const statefromcountrycode = JSON.parse(
          appLocalizer.countries.replace(/&quot;/g, '"')
        )[event.value];
        const country_list_array = [];
        for (const key_country in statefromcountrycode) {
          country_list_array.push({
            label: key_country,
            value: statefromcountrycode[key_country],
          });
        }
        setCountryState(country_list_array);
      }
    } else {
      let prevData = setting[key] || [];
      if (!prevData || typeof prevData == 'string' || prevData == true) {
        prevData = [key];
      }
      prevData = prevData.filter((data) => data != event.target.value);
      if (event.target.checked) {
        prevData.push(event.target.value);
      }
      updateSetting(key, prevData);
    }
  }

  const handleMultiNumberChange = (e, key, optionKey, index) => {
    settingChanged.current = true;
    const mulipleOptions = setting[key] || {};
    mulipleOptions[index] = {
      key: optionKey,
      value: e.target.value,
    };
    updateSetting(key, mulipleOptions);
  };

  const handlMultiSelectDeselectChange = (key, options) => {
    settingChanged.current = true;

    if (Array.isArray(setting[key]) && setting[key].length > 0) {
      updateSetting(key, []);
    } else {
      updateSetting(key, options.filter((option) => {
        return !isProSetting(option.proSetting);
      }).map(({ value }) => value));
    }
  };

  const runUploader = (key) => {
    settingChanged.current = true;
    // Create a new media frame
    const frame = wp.media({
      title: "Select or Upload Media Of Your Chosen Persuasion",
      button: {
        text: "Use this media",
      },
      multiple: false, // Set to true to allow multiple files to be selected
    });

    frame.on("select", function () {
      // Get media attachment details from the frame state
      const attachment = frame.state().get("selection").first().toJSON();
      updateSetting(key, attachment.url);
    });
    // Finally, open the modal on click
    frame.open();
  };

  const isContain = (key, value = null) => {
    let settingValue = setting[key];

    // If setting value is a array
    if (Array.isArray(settingValue)) {
      // Setting value is set
      if (value === null && settingValue.length) {
        return true;
      }

      return settingValue.includes(value)
    }

    // Setting value is not a array
    if (value === null && settingValue) {
      return true;
    }

    return settingValue === value;
  }

  const renderForm = () => {
    return modal.map((inputField, index) => {
      let value = setting[inputField.key] || "";
      let input = "";

      // Filter dependent 
      if (inputField.dependent) {
        if (inputField.dependent.set === true && !isContain(inputField.dependent.key)) {
          return;
        }
        if (inputField.dependent.set === false && isContain(inputField.dependent.key)) {
          return;
        }
        if (inputField.dependent.value && isContain(inputField.dependent.key, inputField.dependent.value)) {
          return;
        }
      }

      // Set input fild based on their type.
      switch (inputField.type) {
        case "text":
        case "url":
        case "password":
        case "email":
        case "number":
          input = (
            <CustomInput.BasicInput
              wrapperClass={`setting-form-input`}
              descClass="settings-metabox-description"
              description={inputField.desc}
              key={inputField.key}
              id={inputField.id}
              name={inputField.name}
              type={inputField.type}
              placeholder={inputField.placeholder}
              value={value}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key);
                }
              }}
              parameter={inputField.parameter}
            />
          );
          break;

        case "textarea":
          input = (
            <CustomInput.TextArea
              wrapperClass="setting-from-textarea"
              inputClass={inputField.class || "form-input"}
              descClass="settings-metabox-description"
              description={inputField.desc}
              key={inputField.key}
              id={inputField.id}
              name={inputField.name}
              placeholder={inputField.placeholder}
              value={value}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key);
                }
              }}
            />
          );
          break;

        case "normalfile":
          input = (
            <CustomInput.BasicInput
              inputClass="setting-form-input"
              type="file"
              key={inputField.key}
              name={inputField.name}
              value={value}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key);
                }
              }}
            />
          );
          break;

        case "file":
          input = (
            <CustomInput.FileInput
              wrapperClass="setting-file-uploader-class"
              descClass="settings-metabox-description"
              description={inputField.desc}
              inputClass={`${inputField.key} form-input`}
              imageSrc={value || appLocalizer.default_logo}
              imageWidth={inputField.width}
              imageHeight={inputField.height}
              buttonClass="btn btn-purple"
              openUploader={appLocalizer.global_string.open_uploader}
              type="hidden"
              key={inputField.key}
              name={inputField.name}
              value={value}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key);
                }
              }}
              onButtonClick={(e) => {
                runUploader(inputField.key);
              }}
            />
          );
          break;

        case "color":
          input = (
            <CustomInput.BasicInput
              wrapperClass="settings-color-picker-parent-class"
              inputClass="setting-color-picker"
              descClass="settings-metabox-description"
              description={inputField.desc}
              key={inputField.key}
              id={inputField.id}
              name={inputField.name}
              type={inputField.type}
              value={value || "#000000"}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key);
                }
              }}
            />
          );
          break;

        case "calender":
          input = (
            <CustomInput.CalendarInput
              wrapperClass="settings-calender"
              inputClass="teal"
              multiple={true}
              value={setting[inputField.key]?.split(",") || ""}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key, "single", inputField.type);
                }
              }}
            />
          );
          break;

        case "map":
          input = (
            <CustomInput.MapsInput
              wrapperClass="settings-basic-input-class"
              inputClass="regular-text"
              descClass="settings-metabox-description"
              description={inputField.desc}
              id="searchStoreAddress"
              placeholder="Enter store location"
              containerId="store-maps"
              containerClass="store-maps, gmap"
              proSetting={isProSetting(inputField.proSetting)}
            />
          );
          break;

        case "button":
          input = (
            <div className="form-button-group">
              <div className="setting-section-divider">&nbsp;</div>
              <label className="settings-form-label"></label>
              <div className="settings-input-content">
                <CustomInput.BasicInput
                  wrapperClass="settings-basic-input-class"
                  inputClass="btn default-btn"
                  descClass="settings-metabox-description"
                  description={inputField.desc}
                  type={inputField.type}
                  placeholder={inputField.placeholder}
                  proSetting={isProSetting(inputField.proSetting)}
                // onChange={handleChange}
                />
              </div>
            </div>
          );
          break;

        case "multi_number":
          input = (
            <CustomInput.MultiNumInput
              parentWrapperClass="settings-basic-input-class"
              childWrapperClass="settings-basic-child-wrap"
              inputWrapperClass="settings-basic-input-child-class"
              innerInputWrapperClass="setting-form-input"
              inputLabelClass="setting-form-input-label"
              idPrefix="setting-integer-input"
              keyName={inputField.key}
              inputClass={inputField.class}
              value={setting[inputField.key]}
              options={inputField.options}
              onChange={handleMultiNumberChange}
              proSetting={isProSetting(inputField.proSetting)}
            />
          );
          break;

        case "radio":
          input = (
            <CustomInput.RadioInput
              wrapperClass="settings-form-group-radio"
              inputWrapperClass="radio-input-label-wrap"
              inputClass="setting-form-input"
              descClass="settings-form-group-radio"
              activeClass="radio-select-active"
              description={inputField.desc}
              value={value}
              name={inputField.name}
              keyName={inputField.key}
              options={inputField.options}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key);
                }
              }}
            />
          );
          break;

        case "radio_select":
          input = (
            <CustomInput.RadioInput
              wrapperClass="form-group-radio-select"
              inputWrapperClass="radioselect-class"
              inputClass="setting-form-input"
              radiSelectLabelClass="radio-select-under-label-class"
              labelImgClass="section-img-fluid"
              labelOverlayClass="radioselect-overlay-text"
              labelOverlayText="Select your Store"
              idPrefix="radio-select-under"
              descClass="settings-metabox-description"
              activeClass="radio-select-active"
              description={inputField.desc}
              type="radio-select"
              value={value}
              name={inputField.name}
              keyName={inputField.key}
              options={inputField.options}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key);
                }
              }}
            />
          );
          break;

        case "radio_color":
          input = (
            <CustomInput.RadioInput
              wrapperClass="form-group-radio-color"
              inputWrapperClass="settings-radio-color "
              inputClass="setting-form-input"
              idPrefix="radio-color-under"
              activeClass="radio-color-active"
              descClass="settings-metabox-description"
              description={inputField.desc}
              type="radio-color"
              value={value}
              name={inputField.name}
              keyName={inputField.key}
              options={inputField.options}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key);
                }
              }}
            />
          );
          break;

        case "toggle_rectangle":
          input = (
            <CustomInput.ToggleRectangle
              wrapperClass="settings-form-group-radio"
              inputWrapperClass="toggle-rectangle-merge"
              inputClass="setting-form-input"
              descClass="settings-metabox-description"
              idPrefix="toggle-rectangle"
              description={inputField.desc}
              value={value}
              name={inputField.name}
              keyName={inputField.key}
              options={inputField.options}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key);
                }
              }}
            />
          );
          break;

        case "select":
          let options = inputField.options;
          input = (
            <CustomInput.SelectInput
              wrapperClass="form-select-field-wrapper"
              descClass="settings-metabox-description"
              description={inputField.desc}
              inputClass={inputField.key}
              options={options}
              value={value}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(data) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  settingChanged.current = true;
                  updateSetting(inputField.key, data.value)
                }
              }}
            />
          );
          break;

        case "country":
          input = (
            <CustomInput.SelectInput
              wrapperClass="country-choice-class"
              descClass="settings-metabox-description"
              description={inputField.desc}
              inputClass={inputField.key}
              options={inputField.options}
              value={value}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e, data) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key, "single", "country", data);
                }
              }}
            />
          );
          break;

        case "state":
          input = (
            <CustomInput.SelectInput
              wrapperClass="state-choice-class"
              descClass="settings-metabox-description"
              description={inputField.desc}
              inputClass={inputField.key}
              options={countryState}
              value={value}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e, data) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key, "single", "select", data);
                }
              }}
            />
          );
          break;

        case "checkbox":
          input = (
            <CustomInput.MultiCheckBox
              wrapperClass="checkbox-list-side-by-side"
              descClass="settings-metabox-description"
              description={inputField.desc}
              selectDeselectClass="select-deselect-trigger"
              inputWrapperClass="toggle-checkbox-header"
              inputInnerWrapperClass="toggle-checkbox-content"
              inputClass={inputField.class}
              hintOuterClass="dashicons dashicons-info"
              hintInnerClass="hover-tooltip"
              idPrefix="toggle-switch"
              selectDeselect={inputField.select_deselect}
              selectDeselectValue="Select / Deselect All"
              rightContentClass="settings-metabox-description"
              rightContent={inputField.right_content}
              options={inputField.options}
              value={value}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key, "multiple");
                }
              }}
              onMultiSelectDeselectChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handlMultiSelectDeselectChange(inputField.key, inputField.options)
                }
              }}
              proChanged={() => setModelOpen(true)}
            />
          );
          break;

        case "table":
          input = (
            <CustomInput.Table
              wrapperClass="settings-form-table"
              tableWrapperClass="settings-table-wrap"
              trWrapperClass="settings-tr-wrap"
              thWrapperClass="settings-th-wrap"
              tdWrapperClass="settings-td-wrap"
              descClass="settings-metabox-description"
              headOptions={inputField.label_options}
              bodyOptions={inputField.options}
            />
          );
          break;

        case "wpeditor":
          input = (
            <CustomInput.WpEditor
              apiKey={appLocalizer.mvx_tinymce_key}
              value={value}
              onEditorChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key, "simple", "wpeditor");
                }
              }}
            />
          );
          break;

        case "label":
          input = (
            <CustomInput.Label
              wrapperClass="form-group-only-label"
              descClass="settings-metabox-description"
              value={inputField.valuename}
              description={inputField.desc}
            />
          );
          break;

        case "section":
          input = (
            <CustomInput.Section
              wrapperClass="setting-section-divider"
              value={inputField.desc}
              hint={inputField.hint}
            />
          );
          break;

        case "blocktext":
          input = (
            <CustomInput.BlockText
              wrapperClass="blocktext-class"
              blockTextClass="settings-metabox-description-code"
              value={inputField.blocktext}
            />
          );
          break;

        case "separator":
          input = <CustomInput.Seperator wrapperClass="mvx_regi_form_box" />;
          break;

        // Special input type project specific
        case "syncbutton":
          input = <SyncNow
            buttonKey={inputField.key}
            apilink={inputField.apilink}
            value={inputField.value}
            description={inputField.desc}
            proSetting={isProSetting(inputField.proSetting)}
            proSettingChanged={() => proSettingChanged(inputField.proSetting)}
            interval={inputField.interval}
            statusApiLink={inputField.statusApiLink}
          />
          break;

        case "sync_map":
          input = <SyncMap
            description={inputField.desc}
            proSetting={isProSetting(inputField.proSetting)}
            proSettingChanged={() => proSettingChanged(inputField.proSetting)}
            value={value}
            onChange={(value) => {
              if (!proSettingChanged(inputField.proSetting) && true) {
                settingChanged.current = true;
                updateSetting(inputField.key, value)
              }
            }}
          />
          break;

        case "testconnection":
          input = <ConnectButton />
          break;

        case "log":
          input = <Log />
          break;

        case "sso_key":
          input = <SSOKey
            value={value}
            description={inputField.desc}
            proSetting={isProSetting(inputField.proSetting)}
            onChange={(value) => {
              if (!proSettingChanged(inputField.proSetting) && true) {
                settingChanged.current = true;
                updateSetting(inputField.key, value)
              }
            }}
          />
          break;

        case "checkbox-default":
          input = (
            <CustomInput.MultiCheckBox
              wrapperClass="checkbox-list-side-by-side"
              descClass="settings-metabox-description"
              description={inputField.desc}
              selectDeselectClass="select-deselect-trigger"
              inputWrapperClass="toggle-checkbox-header"
              inputInnerWrapperClass="default-checkbox"
              inputClass={inputField.class}
              hintOuterClass="checkbox-description"
              hintInnerClass="hover-tooltip"
              idPrefix="toggle-switch"
              selectDeselect={inputField.select_deselect}
              selectDeselectValue="Select / Deselect All"
              rightContentClass="settings-metabox-description"
              rightContent={inputField.right_content}
              options={inputField.options}
              value={value}
              proSetting={isProSetting(inputField.proSetting)}
              onChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handleChange(e, inputField.key, "multiple");
                }
              }}
              onMultiSelectDeselectChange={(e) => {
                if (!proSettingChanged(inputField.proSetting)) {
                  handlMultiSelectDeselectChange(inputField.key, inputField.options)
                }
              }}
              proChanged={() => setModelOpen(true)}
            />
          );
          break;

        case "checkbox-custom-img":
          input = <CheckboxCustomImg
            proSetting={isProSetting(inputField.proSetting)}
            description={inputField.desc}
            value={value}
            onChange={(data) => {
              if (!proSettingChanged(inputField.proSetting)) {
                settingChanged.current = true;
                updateSetting(inputField.key, data)
              }
            }}
          />
          break;

        case "select-custom-radio":
          let option = inputField.options;
          input = <ScheduleInterval
            wrapperClass="form-select-field-wrapper"
            descClass="settings-metabox-description"
            description={inputField.desc}
            inputClass={inputField.key}
            options={option}
            value={value}
            proSetting={isProSetting(inputField.proSetting)}
            onChange={(data) => {
              if (!proSettingChanged(inputField.proSetting)) {
                settingChanged.current = true;
                updateSetting(inputField.key, data.value)
              }
            }}
          />
          break;

      }

      return inputField.type === "section" ||
        inputField.label === "no_label" ? (
        input
      ) : (
        <div key={"g" + inputField.key} className={`form-group ${inputField.classes ? inputField.classes : ''}`}>
          <label
            className="settings-form-label"
            key={"l" + inputField.key}
            htmlFor={inputField.key}
          >
            <p>{inputField.label}</p>
          </label>
          <div className="settings-input-content">{input}</div>
        </div>
      );
    });
  };

  const handleModelClose = () => {
    setModelOpen(false);
  };

  return (
    <>
      <div className="dynamic-fields-wrapper">
        <Dialog
          className="admin-module-popup"
          open={modelOpen}
          onClose={handleModelClose}
          aria-labelledby="form-dialog-title"
        >
          <span
            className="admin-font font-cross"
            onClick={handleModelClose}
          ></span>
          <Popoup />
        </Dialog>
        {successMsg && (
          <div className="admin-notice-display-title">
            <i className="admin-font font-icon-yes"></i>
            {successMsg}
          </div>
        )}
        <form
          className="dynamic-form"
          onSubmit={(e) => {
            handleSubmit(e);
          }}
        >
          {renderForm()}
        </form>
      </div>
    </>
  );
};

export default DynamicForm;