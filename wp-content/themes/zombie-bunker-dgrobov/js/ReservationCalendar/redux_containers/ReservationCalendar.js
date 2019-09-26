import { connect } from "react-redux";
import {
  loadDates,
  toggleModal,
  changeTime,
  changeActive,
  toggleLockIn,
  toggleAgree,
  changeName,
  changeComment,
  changeEmail,
  changePhone,
  sendData,
  setErrors,
  setInputState,
  setLanguage,
  enableYoung,
  disableYoung
} from "../redux_actions";
import ReservationCalendar from "../represantational_components/ReservationCalendar";
const mapStateToProps = state => ({
  activeDate: state.activeDate,
  dates: state.dates,
  loading: state.loading,
  open: state.open,
  selectedTime: state.selectedTime,
  lockedIn: state.lockedIn,
  email: state.email,
  phone: state.phone,
  name: state.fullName,
  young: state.young,
  agrees: state.agrees,
  comment: state.comment,
  sending: state.sending,
  errors: state.errors,
  activeInputs: state.activeInputs,
  lng: state.language,
  coupon: state.coupon
});
const mapDispatchToProps = dispatch => ({
  loadDates: (serviceId, date) => dispatch(loadDates(serviceId, date)),
  toggleModal: () => dispatch(toggleModal()),
  change: date => dispatch(changeActive(date)),
  changeTime: time => dispatch(changeTime(time)),
  toggleLockIn: () => dispatch(toggleLockIn()),
  toggleAgree: () => dispatch(toggleAgree()),
  disableYoung: () => dispatch(disableYoung()),
  enableYoung: () => dispatch(enableYoung()),
  changeName: name => dispatch(changeName(name)),
  changeComment: comment => dispatch(changeComment(comment)),
  changeEmail: email => dispatch(changeEmail(email)),
  changePhone: phone => dispatch(changePhone(phone)),
  sendData: () => dispatch(sendData()),
  setErrors: errors => dispatch(setErrors(errors)),
  setInputState: (name, state) => dispatch(setInputState(name, state)),
  setLanguage: lng => dispatch(setLanguage(lng))
});
export default connect(
  mapStateToProps,
  mapDispatchToProps
)(ReservationCalendar);
