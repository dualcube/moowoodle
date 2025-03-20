import { useState } from 'react';

const Button = (props) => {
    const { customStyle, children, onClick } = props;

    const style = {
        border: `${customStyle.button_border_size ?? 1}px solid ${customStyle.button_border_color ?? '#000000'}`,
        backgroundColor: customStyle.button_background_color ?? '#ffffff',
        color: customStyle.button_text_color ?? '#000000',
        borderRadius: customStyle.button_border_radious + 'px' ?? '0px',
        fontSize: customStyle.button_font_size + 'px' ?? '20px',
        fontWeight: customStyle.button_font_width + 'px' ?? '1rem',
        margin: customStyle.button_margin + 'px' ?? '0px',
        padding: customStyle.button_padding + 'px' ?? '0px',
    };

    const hoverStyle = { 
        border: `1px solid ${customStyle.button_border_color_onhover ?? '#000000'}`,
        color: customStyle.button_text_color_onhover ?? '#000000',
        backgroundColor: customStyle.button_background_color_onhover ?? '#ffffff',
     };

    const [ hovered, setHovered ] = useState( false );

    return (
        <button
            onMouseEnter={() => setHovered(true)}
            onMouseLeave={() => setHovered(false)}
            style={hovered ? hoverStyle : style}
            onClick={onClick}
        >
            {customStyle.button_text || children}
        </button>
    );
}

export default Button;