import { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import { useSetting } from "../../../../contexts/SettingContext";
import Color from "../../../../assets/images/Color.jpg";
import "./ButtonCustomizer.scss";
const Customizer = (props) => {
  const [select, setSelect] = useState("");
  const { setting, updateSetting } = useSetting();
  return (
    <>
      {/* <div className=""> */}
      {/* Heading section */}
      <div className="btn-customizer-menu">
        <div
        title="Change Colors"
          className="btn-customizer-menu-items"
          onClick={(e) => setSelect("color")}
        >
          <img className="color-img" src={Color} alt="" />
        </div>
        <div
        title="Border Style"
          className="btn-customizer-menu-items"
          onClick={(e) => setSelect("border")}
        >
          <i className="admin-font font-crop-free"></i>
        </div>
        <div
        title="Text Style"
          className="btn-customizer-menu-items"
          onClick={(e) => setSelect("font")}
        >
           <i className="admin-font font-text-fields"></i>
        </div>
        <div
        title="Change Size"
          className="btn-customizer-menu-items"
          onClick={(e) => setSelect("size")}
        >
           <i className="admin-font font-resize"></i>
        </div>
        <div
          title="Add Url"
          className="btn-customizer-menu-items"
          onClick={(e) => setSelect("link")}
        >
          <i className="admin-font font-link"></i>
        </div>
      </div>
      {(select === "color" ||
        select === "border" ||
        select === "font" ||
        select === "size" ||
        select === "link") && (
        <div className="customizer-setting-wrapper">
          {/* Wrapper close btn */}
          <button onClick={(e) => setSelect("")} className="wrapper-close">
            <i className="admin-font font-cross"></i>
          </button>

          {/* Render selcted setting */}
          {select === "color" && (
            <div className="color">
              <div className="simple">
                <div className="section">
                  <span className="lable">
                    {__("Background Color", "woocommerce-stock-manager")}
                  </span>
                  <div className="property-section">
                    <input
                      type="color"
                      value={setting.button_background_color}
                      onChange={(e) =>
                        props.onChange(e, "button_background_color")
                      }
                    />
                    <input
                      type="text"
                      value={setting.button_background_color}
                      onChange={(e) =>
                        props.onChange(e, "button_background_color")
                      }
                    />
                  </div>
                </div>
                <div className="section">
                  <span className="lable">
                    {__("Font Color", "woocommerce-stock-manager")}
                  </span>
                  <div className="property-section">
                    <input
                      type="color"
                      value={setting.button_text_color}
                      onChange={(e) => props.onChange(e, "button_text_color")}
                    />
                    <input
                      type="text"
                      value={setting.button_text_color}
                      onChange={(e) => props.onChange(e, "button_text_color")}
                    />
                  </div>
                </div>
              </div>
              <div
                className="hover"
                onMouseEnter={(e) => {
                  props.setHoverOn(true);
                }}
                onMouseLeave={(e) => {
                  props.setHoverOn(false);
                }}
              >
                <div className="section">
                  <span className="lable">
                    {__(
                      "Background Color On Hover",
                      "woocommerce-stock-manager"
                    )}
                  </span>
                  <div className="property-section">
                    <input
                      type="color"
                      value={setting.button_background_color_onhover}
                      onChange={(e) =>
                        props.onChange(e, "button_background_color_onhover")
                      }
                    />
                    <input
                      type="text"
                      value={setting.button_background_color_onhover}
                      onChange={(e) =>
                        props.onChange(e, "button_background_color_onhover")
                      }
                    />
                  </div>
                </div>
                <div className="section">
                  <span className="lable">
                    {__("Font Color On Hover", "woocommerce-stock-manager")}
                  </span>
                  <div className="property-section">
                    <input
                      type="color"
                      value={setting.button_text_color_onhover}
                      onChange={(e) =>
                        props.onChange(e, "button_text_color_onhover")
                      }
                    />
                    <input
                      type="text"
                      value={setting.button_text_color_onhover}
                      onChange={(e) =>
                        props.onChange(e, "button_text_color_onhover")
                      }
                    />
                  </div>
                </div>
              </div>
            </div>
          )}
          {select === "border" && (
            <div className="border">
              <div className="simple">
                <div className="section">
                  <span className="lable">
                    {__("Border Color", "woocommerce-stock-manager")}
                  </span>
                  <div className="property-section">
                    <input
                      type="color"
                      value={setting.button_border_color}
                      onChange={(e) => props.onChange(e, "button_border_color")}
                    />
                    <input
                      onChange={(e) => props.onChange(e, "button_border_color")}
                      type="text"
                      value={setting.button_border_color}
                    />
                  </div>
                </div>
                <div className="section">
                  <span className="lable">
                    {__("Border Size", "woocommerce-stock-manager")}
                  </span>
                  <div className="property-section">
                    <div class="PB-range-slider-div">
                      <input
                        className="PB-range-slider"
                        type="range"
                        value={setting.button_border_size}
                        onChange={(e) =>
                          props.onChange(e, "button_border_size")
                        }
                      />
                      <p class="PB-range-slidervalue">50px</p>
                    </div>
                  </div>
                </div>
                <div className="section">
                  <span className="lable">
                    {__("Border Radious", "woocommerce-stock-manager")}
                  </span>
                  <div className="property-section">
                    <div class="PB-range-slider-div">
                      <input
                        className="PB-range-slider"
                        type="range"
                        value={setting.button_border_radious}
                        onChange={(e) =>
                          props.onChange(e, "button_border_radious")
                        }
                      />
                      <p class="PB-range-slidervalue">50px</p>
                    </div>
                  </div>
                </div>
              </div>
              <div className="hover">
                <div className="section">
                  <span className="lable">
                    {__("Border Color On Hover", "woocommerce-stock-manager")}
                  </span>
                  <div className="property-section">
                    <input
                      type="color"
                      value={setting.button_border_color_onhover}
                      onChange={(e) =>
                        props.onChange(e, "button_border_color_onhover")
                      }
                    />
                    <input
                      type="text"
                      value={setting.button_border_color_onhover}
                      onChange={(e) =>
                        props.onChange(e, "button_border_color_onhover")
                      }
                    />
                  </div>
                </div>
              </div>
            </div>
          )}
          {select === "font" && (
            <div className="font">
              <div className="simple">
                <div className="section">
                  <span className="lable">
                    {__("Font Size", "woocommerce-stock-manager")}
                  </span>
                  <div className="property-section">
                    <div class="PB-range-slider-div">
                      <input
                        className="PB-range-slider"
                        type="range"
                        value={setting.button_font_size}
                        onChange={(e) => props.onChange(e, "button_font_size")}
                      />
                      <p class="PB-range-slidervalue">50px</p>
                    </div>
                  </div>
                </div>
                <div className="section">
                  <span className="lable">
                    {__("Font Width", "woocommerce-stock-manager")}
                  </span>
                  <div className="property-section">
                    <div class="PB-range-slider-div">
                      <input
                        className="PB-range-slider"
                        min={100}
                        max={900}
                        step={100}
                        type="range"
                        value={setting.button_font_width}
                        onChange={(e) => props.onChange(e, "button_font_width")}
                      />
                      <p class="PB-range-slidervalue">50px</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}
          {select === "size" && (
            <div className="size">
              <div className="simple">
                <div className="section">
                  <span className="lable">
                    {__("Padding", "woocommerce-stock-manager")}
                  </span>
                  <div className="property-section">
                    <div class="PB-range-slider-div">
                      <input
                        className="PB-range-slider"
                        type="range"
                        value={setting.button_padding}
                        onChange={(e) => props.onChange(e, "button_padding")}
                      />
                      <p class="PB-range-slidervalue">50px</p>
                    </div>
                  </div>
                </div>
                <div className="section">
                  <span className="lable">
                    {__("Margin", "woocommerce-stock-manager")}
                  </span>
                  <div class="PB-range-slider-div">
                    <input
                      className="PB-range-slider"
                      type="range"
                      value={setting.button_margin}
                      onChange={(e) => props.onChange(e, "button_margin")}
                    />
                    <p class="PB-range-slidervalue">50px</p>
                  </div>{" "}
                </div>
              </div>
            </div>
          )}
          {select === "link" && (
            <div className="link">
              <div className="simple">
                <div className="link-box">
                  <input
                    className="link-input"
                    type="text"
                    placeholder="Paste your url/link"
                  />
                  <button>
                    <i className="admin-font font-send"></i>
                  </button>
                </div>
              </div>
              <p><span>*</span>Keep it blank for default button behavior</p>
            </div>
          )}
        </div>
      )}
      {/* </div> */}
    </>
  );
};
const ButtonCustomizer = (props) => {
  const { buttonText, onChange } = props;
  const [hoverOn, setHoverOn] = useState(false);
  const [buttonHoverOn, setButtonHoverOn] = useState(false);
  const { setting } = useSetting();
  // set style based on hoverIn and hoverOut
  const style = {
    backgroundColor: buttonHoverOn
        ? setting.button_background_color_onhover
        : setting.button_background_color,
    color: buttonHoverOn
        ? setting.button_text_color_onhover
        : setting.button_text_color,
    borderColor: buttonHoverOn
        ? setting.button_border_color_onhover
        : setting.button_border_color,
    borderRadius: setting.button_border_radious + 'px',
    borderWidth: setting.button_border_size + 'px',
    fontSize: setting.button_font_size + 'px',
    fontWeight: setting.button_font_width,
    padding: setting.button_padding + 'px',
    margin: setting.button_margin + 'px',
};

  return (
    <>
      <div className="btn-wrapper">
        <button
          className="btn-preview"
          style={style}
          onClick={(e) => {
            e.preventDefault();
            setHoverOn(!hoverOn);
          }}
          onMouseEnter={(e) => {
            setButtonHoverOn(true);
          }}
          onMouseLeave={(e) => {
            setButtonHoverOn(false);
          }}
        >
          {buttonText}
        </button>
        {hoverOn && (
          <div className="btn-customizer">
            <Customizer
              onChange={props.onChange}
              setHoverOn={setButtonHoverOn}
            />
          </div>
        )}
      </div>
    </>
  );
};
export default ButtonCustomizer;
