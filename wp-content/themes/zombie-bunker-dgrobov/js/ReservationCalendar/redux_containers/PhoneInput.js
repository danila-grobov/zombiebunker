import { connect } from "react-redux";
import PhoneInput from "../represantational_components/PhoneInput";
const mapStateToProps = state => ({
  selectedCountry: state.selectedCountry,
  countries: state.countries
});
const mapDispatchToProps = dispatch => ({
  setSelectedCountry: name =>
    dispatch({ type: "SET_SELECTED_COUNTRY", props: { name } })
});
export default connect(
  mapStateToProps,
  mapDispatchToProps
)(PhoneInput);
