print("Welcome to weight Converter !!!")
while True :
    print("K for kilograms or L for pounds")
    weight=float(input("give the number of the weight you wanna messure : "))
    type=input("kg(k) or bs(L) : ")

    if type == "K" or type == "k"or type == "KG" or type == "kg":

        print(weight,"kilograms, is ",weight * 2.2 , " in pounds")

    elif type == "l" or type == "L" or type == "ls" or type == "LS " or type == "pounds":

        print(weight,"pounds , is ",weight * 0.453 , " in kilograms")

    else:
        print("invalid choice")
    print("do you want to continue ? ")
    x = input("yes or no ?")
    if x == "no" or x == "NO" :
        print("closing program !! cy@")
        break