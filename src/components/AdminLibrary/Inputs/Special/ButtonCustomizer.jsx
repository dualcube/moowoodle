import { useState, useEffect, useRef } from "react";
import { __ } from "@wordpress/i18n";
import "./ButtonCustomizer.scss";

const Customizer = (props) => {
  const { onChange, setting } = props;
  const [select, setSelect] = useState("");
  const [buttonLink, setButtonLink] = useState(setting.button_link);

  useEffect(() => { setButtonLink(setting.button_link) }, [setting.button_link]);

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
          <div className="color-img"></div>
        </div>
        <div
          title="Border Style"
          className="btn-customizer-menu-items"
          onClick={(e) => setSelect("border")}
        >
          <i className="admin-font adminLib-crop-free"></i>
        </div>
        <div
          title="Text Style"
          className="btn-customizer-menu-items"
          onClick={(e) => setSelect("font")}
        >
          <i className="admin-font adminLib-text-fields"></i>
        </div>
        <div
          title="Change Size"
          className="btn-customizer-menu-items"
          onClick={(e) => setSelect("size")}
        >
          <i className="admin-font adminLib-resize"></i>
        </div>
        <div
          title="Add Url"
          className="btn-customizer-menu-items"
          onClick={(e) => setSelect("link")}
        >
          <i className="admin-font adminLib-link"></i>
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
              <i className="admin-font adminLib-cross"></i>
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
                        value={setting.button_background_color ? setting.button_background_color : '#000000'}
                        onChange={(e) => onChange("button_background_color", e.target.value)}
                      />
                      <input
                        type="text"
                        value={setting.button_background_color ? setting.button_background_color : '#000000'}
                        onChange={(e) => props.onChange("button_background_color", e.target.value)}
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
                        value={setting.button_text_color ? setting.button_text_color : '#000000'}
                        onChange={(e) => props.onChange("button_text_color", e.target.value)}
                      />
                      <input
                        type="text"
                        value={setting.button_text_color ? setting.button_text_color : '#000000'}
                        onChange={(e) => props.onChange("button_text_color", e.target.value)}
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
                        value={setting.button_background_color_onhover ? setting.button_background_color_onhover : '#000000'}
                        onChange={(e) => props.onChange("button_background_color_onhover", e.target.value)}
                      />
                      <input
                        type="text"
                        value={setting.button_background_color_onhover ? setting.button_background_color_onhover : '#000000'}
                        onChange={(e) => onChange("button_background_color_onhover", e.target.value)}
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
                        value={setting.button_text_color_onhover ? setting.button_text_color_onhover : '#000000'}
                        onChange={(e) => props.onChange("button_text_color_onhover", e.target.value)}
                      />
                      <input
                        type="text"
                        value={setting.button_text_color_onhover ? setting.button_text_color_onhover : '#000000'}
                        onChange={(e) => props.onChange("button_text_color_onhover", e.target.value)}
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
                        value={setting.button_border_color ? setting.button_border_color : '#000000'}
                        onChange={(e) => props.onChange("button_border_color", e.target.value)}
                      />
                      <input
                        onChange={(e) => props.onChange("button_border_color", e.target.value)}
                        type="text"
                        value={setting.button_border_color ? setting.button_border_color : '#000000'}
                      />
                    </div>
                  </div>
                  <div className="section section-row">
                    <span className="lable">
                      {__("Border Size", "woocommerce-stock-manager")}
                    </span>
                    <div className="property-section">
                      {/* <div class="PB-range-slider-div"> */}
                      <input
                        className="PB-range-slider"
                        type="number"
                        value={setting.button_border_size ? setting.button_border_size : 0}
                        onChange={(e) => props.onChange("button_border_size", e.target.value)}
                      />
                      <p>px</p>
                      {/* <p class="PB-range-slidervalue">{setting.button_border_size ? setting.button_border_size : 0}px</p> */}
                      {/* </div> */}
                    </div>
                  </div>
                  <div className="section section-row">
                    <span className="lable">
                      {__("Border Radious", "woocommerce-stock-manager")}
                    </span>
                    <div className="property-section">
                      {/* <div class="PB-range-slider-div"> */}
                      <input
                        className="PB-range-slider"
                        type="number"
                        value={setting.button_border_radious ? setting.button_border_radious : 0}
                        onChange={(e) => props.onChange("button_border_radious", e.target.value)}
                      />
                      <p>px</p>
                      {/* <p class="PB-range-slidervalue">{setting.button_border_radious ? setting.button_border_radious : 0}px</p>
                    </div> */}
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
                        value={setting.button_border_color_onhover ? setting.button_border_color_onhover : '#000000'}
                        onChange={(e) => props.onChange("button_border_color_onhover", e.target.value)}
                      />
                      <input
                        type="text"
                        value={setting.button_border_color_onhover ? setting.button_border_color_onhover : '#000000'}
                        onChange={(e) => props.onChange("button_border_color_onhover", e.target.value)}
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
                      {__("Button text", "woocommerce-stock-manager")}
                    </span>
                    <div className="property-section">
                      <div class="PB-range-slider-div">
                        <input
                          className="PB-range-slider"
                          type="text"
                          value={setting.button_text}
                          onChange={(e) => props.onChange("button_text", e.target.value)}
                        />
                      </div>
                    </div>
                  </div>
                  <div className="section section-row">
                    <span className="lable">
                      {__("Font Size", "woocommerce-stock-manager")}
                    </span>
                    <div className="property-section">
                      {/* <div class="PB-range-slider-div"> */}
                      <input
                        className="PB-range-slider"
                        type="number"
                        value={setting.button_font_size ? setting.button_font_size : 12}
                        onChange={(e) => props.onChange("button_font_size", e.target.value)}
                      />
                      <p>px</p>
                      {/* <p class="PB-range-slidervalue">{setting.button_font_size ? setting.button_font_size : 12}px</p> */}
                      {/* </div> */}
                    </div>
                  </div>
                  <div className="section section-row">
                    <span className="lable">
                      {__("Font Width", "woocommerce-stock-manager")}
                    </span>
                    <div className="property-section">
                      {/* <div class="PB-range-slider-div"> */}
                      <input
                        className="PB-range-slider"
                        min={100}
                        max={900}
                        step={100}
                        type="number"
                        value={setting.button_font_width ? setting.button_font_width : 400}
                        onChange={(e) => props.onChange("button_font_width", e.target.value)}
                      />
                      <p>px</p>
                      {/* <p class="PB-range-slidervalue">{setting.button_font_width ? setting.button_font_width : 400}</p> */}
                      {/* </div> */}
                    </div>
                  </div>
                </div>
              </div>
            )}
            {select === "size" && (
              <div className="size">
                <div className="simple">
                  <div className="section section-row">
                    <span className="lable">
                      {__("Padding", "woocommerce-stock-manager")}
                    </span>
                    <div className="property-section">
                      {/* <div class="PB-range-slider-div"> */}
                      <input
                        className="PB-range-slider"
                        type="number"
                        value={setting.button_padding ? setting.button_padding : 0}
                        onChange={(e) => props.onChange("button_padding", e.target.value)}
                      />
                      <p>px</p>
                      {/* <p class="PB-range-slidervalue">{setting.button_padding ? setting.button_padding : 0}px</p>
                    </div> */}
                    </div>
                  </div>
                  <div className="section section-row">
                    <span className="lable">
                      {__("Margin", "woocommerce-stock-manager")}
                    </span>
                    <div className="property-section">
                      {/* <div class="PB-range-slider-div"> */}
                        <input
                          className="PB-range-slider"
                          type="number"
                          value={setting.button_margin ? setting.button_margin : 0}
                          onChange={(e) => props.onChange("button_margin", e.target.value)}
                        />
                        <p>px</p>
                        {/* <p class="PB-range-slidervalue">{setting.button_margin ? setting.button_margin : 0}px</p>
                      </div> */}
                    </div>
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
                      value={buttonLink}
                      onChange={(e) => setButtonLink(e.target.value)}
                      placeholder="Paste your url/link"
                    />
                    <button
                      onClick={(e) => {
                        e.preventDefault();
                        e.target.value = buttonLink;
                        props.onChange('button_link', e.target.value);
                      }}
                    >
                      <i className="admin-font adminLib-send"></i>
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
  let { onChange, setting, className } = props;
  const [hoverOn, setHoverOn] = useState(false);
  const [buttonHoverOn, setButtonHoverOn] = useState(false);

  // If setting is not set, set the setting to empty object.
  // empty object represent the default settings.
  setting = setting || {};

  // Set setting helper function


  // set style based on hoverIn and hoverOut
  const style = {
    border: '1px solid transparent',
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

  const buttonRef = useRef();

  useEffect(() => {
    document.body.addEventListener("click", (event) => {
      if (!buttonRef?.current?.contains?.(event.target)) {
        setHoverOn(false);
      }
    })
  }, [])


  return (
    <>
      <div ref={buttonRef} className={`${className ? `${className} ` : ''} btn-wrapper`}>
        <button
          onClick={(e) => {
            e.preventDefault();
            setHoverOn(!hoverOn);
          }}
          className={`btn-preview ${hoverOn && 'active'}`}
          style={style}
          onMouseEnter={(e) => {
            setButtonHoverOn(true);
          }}
          onMouseLeave={(e) => {
            setButtonHoverOn(false);
          }}
        >
          {/* {setting.button_text} */}
          {props.text}
        </button>
        {hoverOn && (
          <div className="btn-customizer">
            <Customizer
              onChange={onChange}
              setHoverOn={setButtonHoverOn}
              setting={setting}
            />
          </div>
        )}
      </div>
    </>
  );
};

export default ButtonCustomizer;
