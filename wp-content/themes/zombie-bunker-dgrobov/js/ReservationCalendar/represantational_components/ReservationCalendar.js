import React, { Component, Fragment } from "react";
import $ from "jquery";
import "../../../scss/modal.scss";
import { PLUGIN_DIR } from "../../../constants";
import Calendar from "./Calendar";
import TimeSelector from "./TimeSelector";
import SessionInfo from "./SessionInfo";
import ContactInfo from "./ContactInfo";
import SuccessMessage from "./MessageDone";
export default class ReservationCalendar extends Component {
  constructor(props) {
    super(props);
    const {
      toggleModal,
      triggerButton,
      serviceId,
      activeDate,
      loadDates,
      setLanguage
    } = props;
    $(document).on("click", triggerButton + "-LT a", () => {
      toggleModal();
      setLanguage("LT");
      loadDates(serviceId, activeDate);
    });
    $(document).on("click", triggerButton + "-EN a", () => {
      toggleModal();
      setLanguage("EN");
      loadDates(serviceId, activeDate);
    });

    var cuponCode = this.getParams(window.location.href).coupon
      ? this.getParams(window.location.href).coupon
      : "";
    var today = new Date();
    today.setHours(today.getHours() + 2);
    document.cookie =
      "coupon=" + cuponCode + "; expires=" + today.toUTCString() + ";";
  }
  getParams = url => {
    let params = {};
    let parser = document.createElement("a");
    parser.href = url;
    let query = parser.search.substring(1);
    let vars = query.split("&");
    for (let i = 0; i < vars.length; i++) {
      let pair = vars[i].split("=");
      params[pair[0]] = decodeURIComponent(pair[1]);
    }
    return params;
  };
  render() {
    const {
      activeDate,
      dates,
      open,
      toggleModal,
      loading,
      loadDates,
      serviceId,
      change,
      changeTime,
      selectedTime,
      toggleLockIn,
      lockedIn,
      changeName,
      changeEmail,
      changePhone,
      changeComment,
      toggleAgree,
      toggleYoung,
      email,
      phone,
      comment,
      name,
      young,
      agrees,
      sendData,
      errors,
      sending,
      setErrors,
      setInputState,
      activeInputs,
      lng,
      coupon
    } = this.props;
    if (open) {
      if (!loading || sending === "DONE") {
        if (sending !== "DONE") {
          return (
            <div className="modal_background" onClick={() => toggleModal()}>
              <div
                className={"modal " + (lockedIn ? "lockedIn" : "notLockedIn")}
                ref={this.modal}
                onClick={e => e.stopPropagation()}
              >
                <div className={"topBar"}>
                  {lockedIn ? (
                    <img
                      src={PLUGIN_DIR + "/imgs/back.svg"}
                      className="backButton clickable"
                      onClick={() => toggleLockIn()}
                    />
                  ) : (
                    ""
                  )}
                  <img
                    src={PLUGIN_DIR + "/imgs/close.svg"}
                    className="closeButton clickable"
                    onClick={() => toggleModal()}
                  />
                </div>
                <div className="main">
                  {!lockedIn ? (
                    <Calendar
                      date={activeDate}
                      dates={dates}
                      loadDates={loadDates}
                      serviceId={serviceId}
                      change={change}
                      lng={lng}
                    />
                  ) : (
                    <ContactInfo
                      changeName={changeName}
                      changeEmail={changeEmail}
                      changePhone={changePhone}
                      changeComment={changeComment}
                      toggleAgree={toggleAgree}
                      toggleYoung={toggleYoung}
                      email={email}
                      phone={phone}
                      name={name}
                      comment={comment}
                      young={young}
                      agrees={agrees}
                      sendData={sendData}
                      sending={sending}
                      errors={errors}
                      setErrors={setErrors}
                      setInputState={setInputState}
                      activeInputs={activeInputs}
                      activeDate={activeDate}
                      dates={dates}
                      lng={lng}
                      coupon={coupon}
                    />
                  )}
                </div>
                <div className="sideBar">
                  {!lockedIn ? (
                    <TimeSelector
                      activeDate={activeDate}
                      changeTime={changeTime}
                      selectedTime={selectedTime}
                      toggleLockIn={toggleLockIn}
                      dates={dates}
                      lng={lng}
                    />
                  ) : (
                    <SessionInfo
                      activeDate={activeDate}
                      selectedTime={selectedTime}
                      dates={dates}
                      lng={lng}
                      coupon={coupon}
                    />
                  )}
                </div>
              </div>
            </div>
          );
        } else {
          return (
            <div className="modal_background" onClick={() => toggleModal()}>
              <div
                className="modal"
                ref={this.modal}
                onClick={e => e.stopPropagation()}
              >
                <div className="topBar">
                  {lockedIn ? (
                    <img
                      src={PLUGIN_DIR + "/imgs/back.svg"}
                      className="backButton clickable"
                      onClick={() => toggleLockIn()}
                    />
                  ) : (
                    ""
                  )}
                  <img
                    src={PLUGIN_DIR + "/imgs/close.svg"}
                    className="closeButton clickable"
                    onClick={() => toggleModal()}
                  />
                </div>
                <SuccessMessage lng={lng} />
              </div>
            </div>
          );
        }
      } else
        return (
          <div className="modal_background" onClick={() => toggleModal()}>
            <div
              className="modal"
              ref={this.modal}
              onClick={e => e.stopPropagation()}
            >
              <div className="topBar">
                <img
                  src={PLUGIN_DIR + "/imgs/close.svg"}
                  className="closeButton clickable"
                  onClick={() => toggleModal()}
                />
              </div>
              <div className="main">
                <img
                  className="loadingIcon"
                  src={PLUGIN_DIR + "/imgs/loading.svg"}
                />
              </div>
              <div className="sideBar"></div>
            </div>
          </div>
        );
    }
    return <div></div>;
  }
}
