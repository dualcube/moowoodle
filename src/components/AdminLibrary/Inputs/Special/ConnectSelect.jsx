import { useState, useEffect } from "react";
import BasicInput from "../BasicInput";

import SelectInput from "../SelectInput";

import { getApiLink, getApiResponse } from "../../../../services/apiService";

import { useSetting } from "../../../../contexts/SettingContext";

import "./ConnectSelect.scss";

const ConnectSelect = (props) => {
  const { mailchimpKey, optionKey, settingChanged } = props;

  // State varaible for list of options
  const { setting, updateSetting } = useSetting();
  const [sellectOption, setSelectOption] = useState(setting[optionKey] || []);
  const [loadings, setLoadings] = useState(false);
  const [showOption, setShowOption] = useState(false);
  const [mailchimpErrorMessage, setMailchimpErrorMessage] = useState('');

  const updateSelectOption = async () => {
    if ( ! setting[ mailchimpKey ] ) {
      setMailchimpErrorMessage('Kindly use a proper MailChimp key.');
    } else {
      setLoadings(true);
      setMailchimpErrorMessage('');
      const options = await getApiResponse(getApiLink(props.apiLink));
      settingChanged.current = true;
      updateSetting(optionKey, options);
      setSelectOption(options);
      setLoadings(false);
      setShowOption(true);
    }
  };

  return (
    <div className="connect-main-wrapper">
      <BasicInput
        wrapperClass="setting-form-input"
        descClass="settings-metabox-description"
        type={ 'text' }
        value={setting[mailchimpKey]}
        proSetting={false}
        onChange={(e) => {
          if ( ! props.proSettingChanged()) {
            props.onChange(e, mailchimpKey);
          }
        }}
      />
  
      <div className="button-wrapper">
        <button
          onClick={(e) => {
            e.preventDefault();
            if ( ! props.proSettingChanged() ) {
              updateSelectOption();
            }
          }}
        >
          Fetch List
        </button>

        {
          loadings && (
          <div class="loader">
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
          </div>
          )
        }
      </div>
      
      { (sellectOption.length || showOption ) &&
        <SelectInput
          onChange={(e) => {
            e = { target: { value: e.value } };
            if ( ! props.proSettingChanged()) {
              props.onChange(e, props.selectKey);
            }
          }}
          options={sellectOption}
          value={props.value}
        />
      }
      
    </div>

  );
};

export default ConnectSelect;