import React, { useEffect, useState, useRef } from "react";
import mapboxgl from 'mapbox-gl';
import MapboxGeocoder from '@mapbox/mapbox-gl-geocoder';

const MapsInput = (props) => {
    const [Lat, setLat] = useState(props.Lat || 22.5726); // Default to Kolkata coordinates
    const [Lng, setLng] = useState(props.Lng || 88.3639);
    const mapContainerRef = useRef(null);
    const markerRef = useRef(null);    
    
    useEffect(() => {
        // Initialize Mapbox
        mapboxgl.accessToken = appLocalizer.mapbox_api;        
        const map = new mapboxgl.Map({
            container: mapContainerRef.current,
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [Lng, Lat],
            zoom: 12,
        });        
        const geocoder = new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            marker: false,
            mapboxgl,
        });        
        
        // Add geocoder control to the map
        map.addControl(geocoder);        
        
        // Create a marker and set it to the current location
        markerRef.current = new mapboxgl.Marker({ color: 'red' })
            .setLngLat([Lng, Lat])
            .addTo(map);        
            
        // Handle result from geocoder and update marker position
        geocoder.on('result', (ev) => {
            const { center } = ev.result;
            setLat(center[1]);
            setLng(center[0]);            
            // Move the marker to the new location
            markerRef.current.setLngLat(center);            
            
            // Call API to save the new latitude and longitude
        });        
        
        // Cleanup on component unmount
        return () => map.remove();
    }, []);    
    
    useEffect(() => {
        // Update the marker position when coordinates change
        if (markerRef.current) {
            markerRef.current.setLngLat([Lng, Lat]);
        }
    }, [Lat, Lng]);    
    
    return (
        <div className={props.wrapperClass}>
            <div
                ref={mapContainerRef} // Reference to the map container
                id={props.containerId || 'maps-container'}
                className={props.containerClass || 'maps-container'}
                style={{ width: '100%', height: '300px' }}
            >
            </div>            
            
            {props.proSetting && <span className="admin-pro-tag">pro</span>}            
            
            {props.description &&
                <p className={props.descClass} dangerouslySetInnerHTML={{ __html: props.description }}>
                </p>
            }
        </div>
    );
};

export default MapsInput;