import { Link } from 'react-router-dom';
import Brand from '../../../assets/images/Brand.png';
import BrandSmall from '../../../assets/images/Brand-small.png';
import "./tabs.scss";
import { useState } from 'react';

const Tabs = ( props ) => {
    const { tabData, currentTab, getForm, prepareUrl, HeaderSection, BannerSection } = props;

    const [menuCol, setMenuCol] = useState(false);
    const [openedSubtab, setOpenedSubtab] = useState('');

    const showTabSection = (tab) => {
        return tab.link ? (
            <a href={ tab.link }>
                { tab.icon && <i className={`admin-font ${ tab.icon }`}></i> }
                { menuCol ? null : tab.name }
            </a>
        ) : (
            <Link
                className={ currentTab === tab.id ? 'active-current-tab' : '' }
                to={ prepareUrl( tab.id ) }
            >
                { tab.icon && <i className={` admin-font ${ tab.icon } `} ></i> }
                { menuCol ? null : tab.name }
                { menuCol  ? null :
                    ( appLocalizer.pro_active == 'free' ) && tab.proDependent &&
                    <span class="admin-pro-tag">Pro</span> 
                }
            </Link>
        );
    }

    const showHideMenu = ( tab ) => {
        return <Link
            className={currentTab === tab.id ? 'active-current-tab' : ''}
            onClick={(e) => {
                e.preventDefault();
                if (openedSubtab == tab.id) {
                    setOpenedSubtab('');
                } else {
                    setOpenedSubtab(tab.id);
                }
            }}
        >
            { tab.icon && <i className={` admin-font ${ tab.icon } `} ></i> }
            {menuCol ? null : tab.name}
            {menuCol ? null : (
                openedSubtab == tab.id ? 
                    <p className='tab-menu-dropdown-icon active'><i className='admin-font font-arrow-right'></i></p>
                    :
                    <p className='tab-menu-dropdown-icon'><i className='admin-font font-arrow-right'></i></p>
            )}
        </Link>
    }
    
    // Get the description of the current tab.
    const getTabDescription = ( tabData ) => {

        return tabData.map( ( {content, type} ) => {
            if ( type === 'file' ) {
                return  content.id === currentTab &&
                    <div className="tab-description-start">
                        <div className="tab-name">{ content.name }</div>
                        <p>{ content.desc }</p>
                    </div>
            } else if ( type === 'folder' ) {
                // Get tabdescription from child by recursion
                return getTabDescription( content );
            }
        });
    }

    const handleMenu =()=>{
        let menudiv = document.getElementById('current-tab-lists');
        menudiv.classList.toggle('active');
    }

    const handleMenuShow = () => {
        setMenuCol(!menuCol);
    }
    
    return (
        <>
            <div className={` general-wrapper ${ props.queryName } `}>
                { HeaderSection && <HeaderSection />}
                <div className="container">
                
                { BannerSection && <BannerSection />}

                <nav className='admin-panel-nav'>
                    <button onClick={handleMenu}><i className='admin-font font-menu'></i></button>
                    <div className='brand'>
                        <img src={Brand} alt="logo" />
                    </div>
                </nav>

                    <div
                        className={ `middle-container-wrapper ${
                            props.horizontally
                                ? 'horizontal-tabs'
                                : 'vertical-tabs'
                        }`}
                    >
                        <div className="middle-child-container">
                            <div id='current-tab-lists' className={`${menuCol ? 'showMenu' : ''} current-tab-lists`}>
                                <div className='current-tab-lists-container'>
                                    <div className='brand'>
                                        {menuCol ? <img src={BrandSmall} alt="logo" /> : <img src={Brand} alt="logo" />}
                                    {menuCol ? null : <p>Stock Manager</p>}
                                        <button onClick={handleMenu} className='menu-close'><i className='admin-font font-cross'></i></button>
                                    </div>

                                    {
                                        tabData.map( ( { type, content } ) => {
                                            
                                            if (type !== 'folder') {
                                                return showTabSection(content)
                                            }

                                            // Tab has child tabs
                                            return <div className='tab-wrapper'>
                                                {
                                                    showHideMenu(content[0].content)
                                                }
                                                {
                                                    // openedSubtab == content[0].content.id &&
                                                    <div className={`subtab-wrapper ${menuCol && 'show'} ${openedSubtab && 'active'}`}>
                                                        {
                                                            content.slice(1).map(({ type, content }) => {
                                                                return showTabSection(content);
                                                            })
                                                        }
                                                    </div>
                                                }
                                            </div>
                                        })
                                    }
                                    <button className='menu-coll-btn' onClick={handleMenuShow}><span><i className='admin-font font-arrow-left'></i></span>{menuCol ? null : 'Collapse'}</button>
                                </div>
                            </div>
                            <div className="tab-content">
                                {/* Render name and description of the current tab */}
                                { getTabDescription( tabData ) }
                                {/* Render the form from parent component for better controll */}
                                { getForm( currentTab )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

export default Tabs;