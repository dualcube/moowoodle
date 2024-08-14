import React, { useEffect, useState, useRef } from "react";
import GoogleMapReact from 'google-map-react';

const AnyReactComponent = ({ text }) => (
	<img src={text} width="38" height="50" />
);

const AutoComplete = ({ map, mapApi, addPlace, placeholder }) => {
    const [autoComplete, setAutoComplete] = useState(null);
    const inputRef = useRef();

    useEffect(() => {
        const options = {
			types: ['address'],
		};
        setAutoComplete(new mapApi.places.Autocomplete(
            this.searchInput,
            options
        ));
		autoComplete.addListener('place_changed', handleOnPlaceChanged);
		autoComplete.bindTo('bounds', map);
    }, []);

    const handleOnPlaceChanged = () => {
        const place = autoComplete.getPlace();
		if (!place.geometry) return;
		if (place.geometry.viewport) {
			map.fitBounds(place.geometry.viewport);
		} else {
			map.setCenter(place.geometry.location);
			map.setZoom(17);
		}
		addPlace(place);
		inputRef.current.blur();
    }

    const clearSearchBox = () => {
        inputRef.current.value = '';
    }

    return (
        <>
            <input
				className="search-input"
				ref={inputRef}
				type="text"
				onFocus={clearSearchBox}
				placeholder={placeholder}
			/>
        </>
    );
}

const GoogleMap = (props) => {
    const [zoom, setZoom] = useState(12);
    const [center, setCenter] = useState(props.center);
    const [draggable, setDraggable] = useState(true);
    const [address, setAddress] = useState();
    const [position, setPosition] = useState({ lat: '', lng: '' });
    const [mapApi, setMapApi] = useState(null);
    const [mapApiLoaded, setMapApiLoaded] = useState(false);
    const [mapInstance, setMapInstance] = useState(null);

    useEffect(() => {
        if ('geolocation' in navigator) {
			navigator.geolocation.getCurrentPosition((position) => {
                setCenter([
                    position.coords.latitude,
                    position.coords.longitude
                ]);
                setPosition({
					lat: position.coords.latitude,
					lng: position.coords.longitude
                });
			});
		}
    }, []);

    const handleOnChange = ({center, zoom}) => {
        setZoom(zoom);
        setCenter(center);
    }

    const handleOnClick = (value) => {
        setPosition({ lat: value.lat, lng: value.lng });
    }

    const onMarkerInteraction = (childKey, childProps, mouse) => {
        setDraggable(false);
        setPosition({ lat: mouse.lat, lng: mouse.lng });
    }

    const onMarkerInteractionMouseUp = () => {
        setDraggable(true);
        generateAddress();
    }

    const apiHasLoaded = (map, maps) => {
        setMapApiLoaded(true);
        setMapInstance(map);
        setMapApi(maps);
		generateAddress();
    }

    const generateAddress = () => {
        const geocoder = new mapApi.Geocoder();

		geocoder.geocode(
			{ location: { lat: position.lat, lng: position.lng } },
			(results, status) => {
				if (status === 'OK') {
					if (results[0]) {
                        setZoom(12);
						setAddress( results[0].formatted_address );
					} else {
						window.alert('No results found');
					}
				} else {
					window.alert('Geocoder failed due to: ' + status);
				}
			}
		);
    }

    return (
        <>
            <div className={props.wrapperClass}>
                {
                    mapApiLoaded && mapInstance &&
                    <AutoComplete
                        map={mapInstance}
                        mapApi={mapApi}
                        addplace={(e) => props.addPlace?.(e, address)}
                        placeholder={props.placeholder}
                    />
                }
                <div style={{ height: '50vh', width: '50%' }}>
                    <GoogleMapReact
                        zoom={zoom}
                        center={center}
                        draggable={draggable}
                        onClick={handleOnClick}
                        onChange={handleOnChange}
                        onChildMouseMove={onMarkerInteraction}
                        onChildMouseDown={onMarkerInteraction}
                        onChildMouseUp={onMarkerInteractionMouseUp}
                        bootstrapURLKeys={{
                            key: appLocalizer.google_api,
                            libraries: ['places', 'geometry'],
                        }}
                        yesIWantToUseGoogleMapApiInternals
                        onGoogleApiLoaded={({ map, maps }) => apiHasLoaded(map, maps)}
                    >
                        <AnyReactComponent text={appLocalizer.marker_icon} />
                    </GoogleMapReact>
                </div>
            </div>
        </>
    );
}

export default GoogleMap;