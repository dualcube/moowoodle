import { useRef, useState } from "react";
import './SSOKey.scss';

const SSOKey = (props) => {
    const { value, proSetting, onChange, description } = props;

    const inputRef = useRef();
    const [ copied, setCopied ] = useState( false );

    function generateRandomKey( length = 8 ) {
        const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        let key = "";
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * characters.length);
            key += characters.charAt(randomIndex);
        }
        return key;
    }

    const generateSSOKey = (e) => {
        e.preventDefault();
        const key = generateRandomKey(8);
        onChange( key );
    }

    const handleCopy = (e) => {
        e.preventDefault();
        navigator.clipboard.writeText(value)
            .then(() => {
                setCopied( true );
            });

            setTimeout(() => {
                setCopied(false);
            }, 2500);
    }

    const handleClear = (e) => {
        e.preventDefault();
        onChange("")
    }

    return (
        <>
            <div className="sso-key-section">
            <div className="input-section">
                <input
                    type="text"
                    value={value}
                    onChange={ (e) => onChange( e.target.value ) }
                />
                { value !== "" &&
                    (
                        <button onClick={ handleClear } className="clear-btn">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 50 59"
                            class="bin"
                        >
                            <path
                            d="M0 7.5C0 5.01472 2.01472 3 4.5 3H45.5C47.9853 3 50 5.01472 50 7.5V7.5C50 8.32843 49.3284 9 48.5 9H1.5C0.671571 9 0 8.32843 0 7.5V7.5Z"
                            ></path>
                            <path
                            d="M17 3C17 1.34315 18.3431 0 20 0H29.3125C30.9694 0 32.3125 1.34315 32.3125 3V3H17V3Z"
                            ></path>
                            <path
                            d="M2.18565 18.0974C2.08466 15.821 3.903 13.9202 6.18172 13.9202H43.8189C46.0976 13.9202 47.916 15.821 47.815 18.0975L46.1699 55.1775C46.0751 57.3155 44.314 59.0002 42.1739 59.0002H7.8268C5.68661 59.0002 3.92559 57.3155 3.83073 55.1775L2.18565 18.0974ZM18.0003 49.5402C16.6196 49.5402 15.5003 48.4209 15.5003 47.0402V24.9602C15.5003 23.5795 16.6196 22.4602 18.0003 22.4602C19.381 22.4602 20.5003 23.5795 20.5003 24.9602V47.0402C20.5003 48.4209 19.381 49.5402 18.0003 49.5402ZM29.5003 47.0402C29.5003 48.4209 30.6196 49.5402 32.0003 49.5402C33.381 49.5402 34.5003 48.4209 34.5003 47.0402V24.9602C34.5003 23.5795 33.381 22.4602 32.0003 22.4602C30.6196 22.4602 29.5003 23.5795 29.5003 24.9602V47.0402Z"
                            clip-rule="evenodd"
                            fill-rule="evenodd"
                            ></path>
                            <path d="M2 13H48L47.6742 21.28H2.32031L2 13Z"></path>
                        </svg>
                        </button>
                    )
                }
            </div>
            { value !== "" ?
                (
                    <button
                        className="copy-btn"
                        onClick={ handleCopy }
                    >   {copied && <span className="tooltip">✔copied</span>}
                        <svg viewBox="0 0 6.35 6.35" y="0" x="0" height="20" width="20" version="1.1" class="clipboard">
                            <g>
                            <path fill="currentColor" d="M2.43.265c-.3 0-.548.236-.573.53h-.328a.74.74 0 0 0-.735.734v3.822a.74.74 0 0 0 .735.734H4.82a.74.74 0 0 0 .735-.734V1.529a.74.74 0 0 0-.735-.735h-.328a.58.58 0 0 0-.573-.53zm0 .529h1.49c.032 0 .049.017.049.049v.431c0 .032-.017.049-.049.049H2.43c-.032 0-.05-.017-.05-.049V.843c0-.032.018-.05.05-.05zm-.901.53h.328c.026.292.274.528.573.528h1.49a.58.58 0 0 0 .573-.529h.328a.2.2 0 0 1 .206.206v3.822a.2.2 0 0 1-.206.205H1.53a.2.2 0 0 1-.206-.205V1.529a.2.2 0 0 1 .206-.206z"></path>
                            </g>
                        </svg>
                    </button>
                )
                :
                (
                    <button
                        className="generate-btn"
                        onClick={ generateSSOKey }
                    >
                        <svg height="24" width="24" fill="#FFFFFF" viewBox="0 0 24 24" data-name="Layer 1" id="Layer_1" class="sparkle">
                            <path d="M10,21.236,6.755,14.745.264,11.5,6.755,8.255,10,1.764l3.245,6.491L19.736,11.5l-6.491,3.245ZM18,21l1.5,3L21,21l3-1.5L21,18l-1.5-3L18,18l-3,1.5ZM19.333,4.667,20.5,7l1.167-2.333L24,3.5,21.667,2.333,20.5,0,19.333,2.333,17,3.5Z"></path>
                        </svg>
                        <span class="text">Generate</span>
                    </button>
                )
            }
            {
                proSetting && <span className="admin-pro-tag">pro</span>
            }
        </div>
        <p className="settings-metabox-description" dangerouslySetInnerHTML={{__html: description}}></p>
        </>
    );
}

export default SSOKey;