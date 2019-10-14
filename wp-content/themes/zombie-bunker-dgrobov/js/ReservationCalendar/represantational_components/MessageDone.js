import React, { Component } from "react";
import { connect } from "react-redux";
import "../../../scss/messageDone.scss";
export class MessageDone extends Component {
  render() {
    const { date, time, lng } = this.props;
    return (
      <div className="messageDone">
        <div className="message">
          <span className="thanks">
            {lng === "LT"
              ? "Ačiū už rezervaciją!"
              : "Thank you for the reservation!"}
          </span>
          <span className="info">
            {lng === "LT" ? "Lauksime jūsų:" : "We'll be waiting for you at:"}
            <br />
            <span className="red">
              {date +
                " " +
                moment("00:00", "HH:mm")
                  .minutes(time)
                  .format("HH:mm")}
            </span>
          </span>
          <span className="locationInfo">
            {lng === "LT"
              ? 'Į lokaciją vykstama savo transportu (šalia Nemenčinės, degalinė "EMSI"), į tikslią vietą palydi priešzombinio būrio specialas.'
              : 'You will have to arrive to the destination independently ("EMSI" gas station near Nemenčinė), you will be guided to the exact location afterwards. '}
          </span>
        </div>
      </div>
    );
  }
}

const mapStateToProps = state => ({
  date: state.activeDate,
  time: state.selectedTime
});

const mapDispatchToProps = {};

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(MessageDone);
