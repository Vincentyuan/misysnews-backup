
export class FeedOriginal {
  // the property here is get from the cusomize post
  postID : number;// the post id of this feild
  id : string ;
  // feed_name: string; // =>feedName in feedValue
  url: string; // =>url get from category (done in the backend)
  valid_time_from : string;
  valid_time_to : string;
  background_image : string; // => background in FeedValue
  // description : string;
  category: string ;
  category_id :string;
  // check_box_fix_background:string;
  title:string; // the title of the customize post
  text :string;//help property  keep it
  constructor(){
    this.text = "";
  }
}

//this static field should be the same to the backend
export class RequestType{
  static customized = "0";
  static whole = "1";

}
