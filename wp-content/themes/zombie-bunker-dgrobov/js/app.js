import "../scss/main.scss";
import $ from "jquery";
import React from "react";
import ReactDOM from "react-dom";
import HamburgerMenu from "./HamburgerMenu";
import ReservationCalendar from "./ReservationCalendar/redux_containers/ReservationCalendar";
import ZombieMap from "./ZombieMap";
import { createStore, applyMiddleware } from "redux";
import thunk from "redux-thunk";
import { Provider } from "react-redux";
import rootReducer from "./ReservationCalendar/redux_reducers";
const store = createStore(rootReducer, applyMiddleware(thunk));
$(() => {
  // added menu button
  ReactDOM.render(
    <HamburgerMenu src={$(".menu_button img")[0].currentSrc} />,
    $(".menu_button")[0]
  );
  // // added modal
  ReactDOM.render(
    <Provider store={store}>
      <ReservationCalendar triggerButton={".open-modal"} serviceId={2} />
    </Provider>,
    $(".reservation_modal")[0]
  );
  ReactDOM.render(<ZombieMap />, $("#map")[0]);
});
