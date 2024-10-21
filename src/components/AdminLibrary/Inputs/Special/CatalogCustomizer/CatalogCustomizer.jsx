import React, { useState, useEffect, useRef } from 'react';
import ButtonCustomizer from '../ButtonCustomizer';
import SubTabSection from '../SubTabSection/SubTabSection';
import Sample_Product from '../../../../../assets/images/sample-product.jpg';

import ReactDragListView from "react-drag-listview";
import './CatalogCustomizer.scss';

const CatalogCustomizer = (props) => {

  const [buttonSetting, setButtonSetting] = useState({});

  const { onChange, proSetting } = props;

  // State variable for manage setting locally
  // Manage state variable locally better management
  const [setting, _setSetting] = useState(props.setting);

  const setSetting = (key, value) => {
    _setSetting({ ...setting, [key]: value });
    onChange(key, value);
  }

  const shopPagePossitionSetting = setting['shop_page_possition_setting'] || [];
  const buttonPossitionSetting = setting['shop_page_button_position_setting'] || [];

  // Create menu
  const [menu, setMenu] = useState([
    {
      name: "Enquiry", link: "hi", id: 'enquiry', icon: 'font-info',
    },
    {
      name: "Quote", link: "hi", id: 'quote', icon: 'font-payment',
    },
    {
      name: "Catalog", link: "catalog", id: 'catalog', icon: 'font-payment',
    },
  ]);

  // Set default current tab
  const [currentTab, setCurrentTab] = useState(menu[0]);

  // Create default dragand drop items.
  const [dragableItems, setDragableItems] = useState([
    {
      id: 'price_section',
      content: () => {
        const [hideProductPrice, setHideProductPrice] = useState(setting['hide_product_price']);
        return (
          <div className='price-section toggle-visibility'>
            <div
              onClick={() => {
                setHideProductPrice(!hideProductPrice);
                setSetting('hide_product_price', !hideProductPrice);
              }}
              className='button-visibility'
            >
              <i className='admin-font font-support'></i>
            </div>
            <p className='product-price' style={{ opacity: hideProductPrice ? "0.3" : "1" }}><span className='strikethrough'>$20.00</span> $18.00</p>
          </div>
        )
      },
      defaultPosition: 0,
      dragable: false,
    },
    {
      id: 'product_description',
      content: () => {
        const [hideProductDesc, setHideProductDesc] = useState(setting['hide_product_desc']);
        return (
          <div className='description-section toggle-visibility'>
            <div
              onClick={() => {
                setHideProductDesc(!hideProductDesc);
                setSetting('hide_product_desc', !hideProductDesc)
              }}
              className='button-visibility'
            >
              <i className='admin-font font-support'></i>
            </div>
            <p className='product-description' style={{ opacity: hideProductDesc ? "0.3" : "1" }}>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>
          </div>
        )
      },
      defaultPosition: 1,
      dragable: false,
    },
    {
      id: 'additional_input',
      defaultPosition: 2,
      dragable: proSetting ? true : false,
    },
    {
      id: 'add_to_cart',
      content: () => (
        <>
          <section className='catalog-add-to-cart-section'>
            <div className='catalog-add-to-cart-quantity'>1</div>
            <div class="catalog-add-to-cart-btn">Add to cart</div>
          </section>
        </>
      ),
      defaultPosition: 3,
      dragable: false,
    },
    {
      id: 'sku_category',
      content: () => (
        <div className='product-sku-category'>
          <p>SKU: <span>WOO-ALBUM</span></p>
          <p>Category: <span>Music</span></p>
        </div>
      ),
      defaultPosition: 4,
      dragable: false,
    },
    {
      id: 'custom_button',
      content: 'buttonDND',
      defaultPosition: 5,
      dragable: proSetting ? true : false,
    },
  ]);

  // Create default button drag and drop items.
  const [buttonItems, setButtonItems] = useState([
    { id: 'enquery_button' },
    { id: 'quote_button' },
    { id: 'enquery_cart_button' }
  ]);

  /**
   * Get the index of list item by id.
   * @param {*} list 
   * @param {*} id 
   * @returns 
   */
  const getIndex = (list, id) => {
    let foundItemIndex = -1;

    list.forEach((item, index) => {
      if (item.id === id) {
        foundItemIndex = index;
      }
    });

    return foundItemIndex;
  }

  /**
   * Reorder the elements
   * @param {*} list 
   * @param {*} startIndex 
   * @param {*} endIndex 
   * @returns 
   */
  const reorder = (list, startIndex, endIndex) => {
    const result = Array.from(list);
    const [removed] = result.splice(startIndex, 1);
    result.splice(endIndex, 0, removed);
    return result;
  }


  // Set dragable item to its previously set sequence sequenced at start.
  useEffect(() => {

    let possitionSetting = shopPagePossitionSetting || {};
    let items = [...dragableItems];

    possitionSetting = Object.entries(possitionSetting);

    // Check they are going in same position
    let samePosition = true;
    let possitionToMove = null;
    possitionSetting.forEach(([willMove, moveAfter]) => {
      moveAfter;

      if (possitionToMove !== null && possitionToMove != moveAfter) {
        samePosition = false;
      }

      possitionToMove = moveAfter;
    });

    possitionSetting.forEach(([willMove, moveAfter]) => {
      let startIndex = getIndex(items, willMove);
      let endIndex = getIndex(items, moveAfter) + 1;

      // If they are in same position insert it to the last this maintain the sequence properly
      if (samePosition && possitionToMove !== null) {
        endIndex = items.length;
      }

      items = reorder(items, startIndex, endIndex);
    });

    // Take action when movable elements are in same position
    if (samePosition && possitionToMove !== null) {
      const movedElements = items.splice(items.length - 2, 2);

      // Find index where the moved element get position
      const movedIndex = getIndex(items, possitionSetting[0][1]) + 1;

      // Create new sequence of items
      items = [...items.slice(0, movedIndex), ...movedElements, ...items.slice(movedIndex)]
    }

    setDragableItems(items);

  }, []);

  // Set button dragable item to its previously set sequence sequenced at start.
  useEffect(() => {
    setButtonItems((buttonItems) => {
      buttonItems.sort((a, b) => buttonPossitionSetting.indexOf(a.id) - buttonPossitionSetting.indexOf(b.id));
      return buttonItems;
    });
  }, []);

  /**
   * Function after drag end. Set settings
   * @param {*} result 
   * @returns 
   */
  const onDragEnd = (startIndex, endIndex) => {
    if (endIndex != 0 && !endIndex) {
      return;
    }

    const newItems = reorder(dragableItems, startIndex, endIndex);

    // Calculate position for dragable items.
    const shopPageBildersPosition = {};
    let positionAfter = '';

    newItems.forEach((item, index) => {
      if (item.dragable) {
        shopPageBildersPosition[item.id] = positionAfter;
      } else {
        positionAfter = item.id;
      }
    });

    setSetting('shop_page_possition_setting', shopPageBildersPosition);

    setDragableItems(newItems);
  };

  /**
   * Function after button drag end. Set settings
   * @param {*} result 
   * @returns 
   */
  const onButtonDragEnd = (startIndex, endIndex) => {
    if (endIndex != 0 && !endIndex) {
      return;
    }

    const newItems = reorder(buttonItems, startIndex, endIndex);

    // Calculate position for dragable items.
    const position = newItems.map(item => item.id);

    setSetting('shop_page_button_position_setting', position);

    setButtonItems(newItems);
  }

  /**
   * Component for button dnd
   * @param {*} props 
   * @returns 
   */

  const handleSubMenuChange = (newTab) => {
    if (currentTab.id === newTab.id) return;

    setCurrentTab({ ...newTab });

    let mainWrapper = document.getElementById('catelog-customizer-main-wrapper');
    window.scrollTo(0, 0)

    mainWrapper.classList.add(newTab.id)
    mainWrapper.classList.add('change-tab');

    setTimeout(() => {
      mainWrapper.classList.remove('change-tab');

      setTimeout(() => {
        mainWrapper.classList.remove(newTab.id)
      }, 300)

    }, 500);
  }


  return (
    <>
      {/* Render upper tab sections */}
      <SubTabSection
        menuitem={menu}
        currentTab={currentTab}
        setCurrentTab={setCurrentTab}
        setting={setting}
        onChange={props.onChange}
      />

      {/* Render shop page sections */}
      <main className='catelog-customizer-main-wrapper ' id='catelog-customizer-main-wrapper'>
        <section className='catelog-customizer'>
          <div className='product-img'>
            <img src={Sample_Product} alt="" />
          </div>
          <div className='product-data'>
            <h1 className='product-name'>Sample Product</h1>
            <div className='drag-drop-component'>
              {/* Render default shop pages drag and drop */}
              <ReactDragListView
                nodeSelector=".shop-page-draggable"
                handleSelector=".should-move"
                lineClassName="dragLine"
                ignoreSelector='.ignore-drag'
                onDragEnd={(fromIndex, toIndex) => onDragEnd(fromIndex, toIndex)}
              >
                {
                  dragableItems.map((item, index) => (
                    <div
                      className={`${item.dragable ? 'should-move' : ''} shop-page-draggable`}
                    >
                      {
                        item.content === 'buttonDND' ?
                          <div className='button-wrapper'>
                            {/* Render default shop pages drag and drop */}
                            <ReactDragListView
                              nodeSelector=".shop-page-button-draggable"
                              lineClassName="dragLine"
                              handleSelector={proSetting ? ".shop-page-button-draggable" : "none"}
                              onDragEnd={(fromIndex, toIndex) => proSetting && onButtonDragEnd(fromIndex, toIndex)}
                            >
                              {buttonItems.map(item => (
                                <div key={item.id} className='shop-page-button-draggable'>
                                  {item.id === 'enquery_button' && (
                                    <div
                                      onClick={() => { handleSubMenuChange(menu[0]); }}
                                      className={`button-main-container toggle-visibility ${currentTab.id === "enquiry" ? '' : 'disable'}`}
                                    >
                                      <button className='button-visibility'><i className='admin-font font-support'></i></button>
                                      <ButtonCustomizer
                                        className='ignore-drag'
                                        text='enquiry'
                                        setting={setting['enquery_button']}
                                        onChange={(key, value) => {
                                          const previousSetting = setting['enquery_button'] || {};
                                          setSetting('enquery_button', { ...previousSetting, [key]: value });
                                        }}
                                      />
                                    </div>
                                  )}
                                  {item.id === 'cart_button' && (
                                    <ButtonCustomizer
                                      text='Add to cart'
                                      setting={setting['cart_button']}
                                      onChange={(key, value) => {
                                        const previousSetting = setting['cart_button'] || {};
                                        setSetting('cart_button', { ...previousSetting, [key]: value });
                                      }}
                                    />
                                  )}
                                  {item.id === 'quote_button' && (
                                    <div
                                      onClick={() => { handleSubMenuChange(menu[1]); }}
                                      className={`button-main-container toggle-visibility ${currentTab.id === "quote" ? '' : 'disable'}`}
                                    >
                                      <button className='button-visibility'><i className='admin-font font-support'></i></button>
                                      <ButtonCustomizer
                                        text='Add to quote'
                                        setting={setting['quote_button']}
                                        onChange={(key, value) => {
                                          const previousSetting = setting['quote_button'] || {};
                                          setSetting('quote_button', { ...previousSetting, [key]: value });
                                        }}
                                      />
                                    </div>
                                  )}
                                </div>
                              ))}
                            </ReactDragListView>
                          </div>
                          :
                          item.id === 'additional_input' ?
                            <div onClick={() => {
                              handleSubMenuChange(menu[2])
                            }}
                              className={`additional-input toggle-visibility ${currentTab.id === 'catalog' ? '' : 'disable'}`}>
                              {/* {console.log("inputbox",currentTab)}
                            {console.log("menu",menu)} */}
                              <button className='button-visibility'><i className='admin-font font-support'></i></button>
                              <input
                                placeholder='Additional input(optional)'
                                type='text'
                                value={setting['additional_input']}
                                onChange={(e) => {
                                  // setValue(e.target.value);
                                  setSetting('additional_input', e.target.value);
                                }}
                              />
                            </div>
                            :
                            <item.content currentTab={currentTab} setCurrentTab={setCurrentTab} />
                      }
                    </div>
                  ))}
              </ReactDragListView>
            </div>
            {!proSetting &&
              <article className='pro-banner'>
                <p>Upgrade to pro for endless customization</p>
                <a href="#" target='_blank'>Upgrade now</a>
              </article>
            }
          </div>
        </section>
        <section className='single-product-page-description'>
          <div className='option'>
            <ul>
              <li className='active'>Description <span><i className='admin-font font-keyboard_arrow_down'></i></span></li>
              <li>Additional Information</li>
              <li>Review</li>
            </ul>
          </div>
          <div className='description'>
            <h2>Description</h2>
            <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
          </div>
        </section>
      </main>
    </>
  );
}

export default CatalogCustomizer;
