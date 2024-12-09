import React, { useState } from 'react'
import Dialog from "@mui/material/Dialog";
import Popoup from '../PopupContent/PopupContent';
import './banner.scss';

export default function banner() {
    if(localStorage.getItem('banner') != 'false'){
        localStorage.setItem("banner", true);
    }
    const [ modal, setModal ] = useState(false);
    const [ banner, setBanner ] = useState(localStorage.getItem('banner') == 'true' ? true : false);

    const handleCloseBanner = () => {
		localStorage.setItem('banner',false)
        setBanner(false);
	}

	const handleClose = () => {
        setModal(false);
	}
	const handleOpen = () => {
        setModal(true);
	}
    if(banner){
        document.addEventListener('DOMContentLoaded', function () {
            const carouselItems = document.querySelectorAll('.carousel-item');
            const totalItems = carouselItems.length;
            let currentIndex = 0;
            let interval;
        
            // Function to show the current slide and hide others
            function showSlide(index) {
                carouselItems.forEach(item => item.classList.remove('active'));
                carouselItems[index].classList.add('active');
            }
        
            // Function to go to the next slide
            function nextSlide() {
                currentIndex = (currentIndex + 1) % totalItems;
                showSlide(currentIndex);
            }
        
            // Function to go to the previous slide
            function prevSlide() {
                currentIndex = (currentIndex - 1 + totalItems) % totalItems;
                showSlide(currentIndex);
            }
        
            // Start the auto-slide interval
            function startAutoSlide() {
                interval = setInterval(nextSlide, 7000); // Change slide every 7 seconds
            }
        
            // Stop the auto-slide interval
            function stopAutoSlide() {
                clearInterval(interval);
            }
        
            // Initialize the carousel
            showSlide(currentIndex);
            startAutoSlide();
        
            // Handle next button click
            document.getElementById('nextBtn').addEventListener('click', function () {
                nextSlide();
                stopAutoSlide();
                startAutoSlide();
            });
        
            // Handle previous button click
            document.getElementById('prevBtn').addEventListener('click', function () {
                prevSlide();
                stopAutoSlide();
                startAutoSlide();
            });
        });
    }
    

    return (
        <>
            { ! appLocalizer.khali_dabba ? 
                banner ?
                    <div className="custom-banner">
                        <Dialog
                            className="admin-module-popup"
                            open={modal}
                            onClose={handleClose}
                            aria-labelledby="form-dialog-title"
                        >	
                            <span 
                                className="admin-font adminLib-cross stock-manager-popup-cross"
                                onClick={handleClose}
                            ></span>
                            <Popoup/>
                        </Dialog>
                        <div className="admin-carousel-container">
                            <div className="carousel-container">
                                <div class="admin-font adminLib-cross pro-slider-cross" onClick={handleCloseBanner}></div>
                                <div class="why-go-pro-tag" onClick={handleOpen}>Why Premium</div>
                                <ul className="carousel-list">
                                    <li className="carousel-item active">
                                        <div className="admin-pro-txt-items">
                                            <h3>Automated user and course synchronization with scheduler {' '}</h3>
                                            <p>Utilize personalized scheduling options to synchronize users and courses between WordPress and Moodle.{' '}</p>
                                            <a
                                                href={appLocalizer.shop_url}
                                                target='_blank'
                                                className="admin-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                    <li class="carousel-item">
                                        <div className="admin-pro-txt-items">
                                            <h3>Convenient Single Sign-On login{' '}</h3>
                                            <p>SSO enables students to access their purchased courses without the need to log in separately to the Moodle site.{' '}</p>
                                            <a
                                                href={appLocalizer.shop_url}
                                                target='_blank'
                                                className="admin-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                    <li class="carousel-item">
                                        <div className="admin-pro-txt-items">
                                            <h3>Steady Income through Course Subscriptions {' '}</h3>
                                            <p>Generate consistent revenue by offering courses with subscription-based model.{' '}</p>
                                            <a
                                                href={appLocalizer.shop_url}
                                                target='_blank'
                                                className="admin-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                    <li class="carousel-item">
                                        <div className="admin-pro-txt-items">
                                            <h3>Synchronize Courses in Bulk{' '}</h3>
                                            <p>Effortlessly synchronize multiple courses  at once, ideal for managing large course catalogs.{' '}</p>
                                            <a
                                                href={appLocalizer.shop_url}
                                                target='_blank'
                                                className="admin-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                    <li class="carousel-item">
                                        <div className="admin-pro-txt-items">
                                            <h3>Automatic User Synchronization for Moodle™ and WordPress{' '}</h3>
                                            <p>Synchronizes user accounts between Moodle™ and WordPress, ensuring consistent user management across both platforms without manual intervention.{' '}</p>
                                            <a
                                                href={appLocalizer.shop_url}
                                                target='_blank'
                                                className="admin-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="carousel-controls">
                                <button id="prevBtn"><i className='admin-font adminLib-arrow-left'></i></button>
                                <button id="nextBtn"><i className='admin-font adminLib-arrow-right'></i></button>
                            </div>
                        </div>
                    </div>
                : ''
            : ''
            }
        </>
    );
}