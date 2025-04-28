import React, { useState, useEffect } from 'react'
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
    
    useEffect(() => {
        if (!banner) return;

        const carouselItems = document.querySelectorAll('.carousel-item');
        const totalItems = carouselItems.length;
        if (!totalItems) return;
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
    }, [banner]);  

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
                                            <h3>This is a sample banner</h3>
                                            <p>Sample banner description</p>
                                            <a
                                                // href={appLocalizer.pro_url}
                                                target='_blank'
                                                className="admin-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                    <li class="carousel-item">
                                    <div className="admin-pro-txt-items">
                                            <h3>This is a example banner</h3>
                                            <p>example banner description</p>
                                            <a
                                                // href={appLocalizer.pro_url}
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
