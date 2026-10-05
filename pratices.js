function count_max() {
  let a = 0;
  let b = 1;
  for (i = 0; i <= 1; i++) {
    let final = a + b;
    console.log(`${a} + ${b} = ${final}`);

    a = b ;
    b = final;
  }
}

// count_max();


let str = "madam";

let reverse = str.split("").reverse().join("");

if (str === reverse) {
    console.log("Palindrome");
} else {
    console.log("Not Palindrome");
}