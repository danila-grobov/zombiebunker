import React, { Component, createRef } from "react";
import { connect } from "react-redux";
import { changeEmail, setInputState, setErrors } from "../redux_actions";
import EmailInput from "react-email-autocomplete";
import $ from "jquery";
class Email extends Component {
  constructor(props) {
    super(props);
    this.email = createRef();
  }
  inputElement = null;
  componentDidMount = () => {
    const { name, setInputState, errors, changeEmail, setErrors } = this.props;
    this.inputElement = this.email.current.textHandler;
    this.inputElement.onclick = () => {
      setInputState(name, true);
    };
    this.inputElement.onblur = e => {
      if (e.currentTarget.value === "") setInputState(name, false);
    };
    this.inputElement.onchange = e => {
      setInputState(name, true);
      let errorTemp = { ...errors };
      delete errorTemp[name];
      setErrors(errorTemp);
      changeEmail(e.currentTarget.value);
    };
  };
  render() {
    const { name, errors, value, label, activeInputs } = this.props;
    let className = "";
    if (errors[name]) className += "error ";
    if (
      activeInputs.find(input => input.name == name) &&
      activeInputs.find(input => input.name == name).state
    )
      className += "active ";
    if (this.inputElement)
      $(this.inputElement)
        .parent()
        .attr("class", className);
    return (
      <div className="input">
        <EmailInput type="email" id={name} ref={this.email} value={value} />
        <label htmlFor={name}>{label}</label>
        {errors[name] ? (
          <div className="errorText">{"- " + errors[name]}</div>
        ) : (
          ""
        )}
      </div>
    );
  }
}

const mapStateToProps = state => {
  return {
    errors: state.errors,
    value: state.email,
    activeInputs: state.activeInputs
  };
};

const mapDispatchToProps = dispatch => ({
  changeEmail: email => dispatch(changeEmail(email)),
  setInputState: (name, state) => dispatch(setInputState(name, state)),
  setErrors: errors => dispatch(setErrors(errors))
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(Email);
